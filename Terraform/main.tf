terraform {
  required_version = ">= 0.12"

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "4.49.0"
    }
  }

}

provider "aws" {

  region = var.aws_region

}

data "aws_ecs_task_definition" "tskDef-med" {
  task_definition = aws_ecs_task_definition.tskDef-med.family
}

#Creating VPC

module "vpc" {
  source = "terraform-aws-modules/vpc/aws"

  name = "vpc-medcloud"
  cidr = "10.0.0.0/16"

  azs             = ["${var.aws_region}a", "${var.aws_region}b", "${var.aws_region}c"]
  private_subnets = ["10.0.1.0/24", "10.0.2.0/24", "10.0.3.0/24"]
  public_subnets  = ["10.0.101.0/24", "10.0.102.0/24", "10.0.103.0/24"]

  enable_nat_gateway = true
  enable_vpn_gateway = true

  create_database_subnet_group           = true
  create_database_subnet_route_table     = true
  create_database_internet_gateway_route = true

  enable_dns_hostnames = true
  enable_dns_support   = true


  tags = {
    Terraform   = "true"
    Environment = "dev"
  }
}

#Creating Security Groupy

module "web_server_sg" {
  source = "terraform-aws-modules/security-group/aws//modules/http-80"

  name        = "http-mysql"
  description = "Security group for web-server with HTTP and MySQLports open within VPC"
  vpc_id      = module.vpc.vpc_id

  ingress_cidr_blocks = ["0.0.0.0/0"]
}

#Adding more rule

resource "aws_security_group_rule" "mysql" {
  type              = "ingress"
  from_port         = 3306
  to_port           = 3306
  protocol          = "tcp"
  cidr_blocks       = ["0.0.0.0/0"]
  ipv6_cidr_blocks  = []
  security_group_id = module.web_server_sg.security_group_id
}

#Creating Bucket

module "s3_bucket" {
  source = "terraform-aws-modules/s3-bucket/aws"

  bucket = var.bucket_name
  acl    = "private"

  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true

  versioning = {
    enabled = false
  }

}



#Creating DataBase

resource "aws_db_subnet_group" "medcloud_db_group" {
  name       = "medcloud"
  subnet_ids = [module.vpc.public_subnets[0], module.vpc.public_subnets[1]]

  tags = {
    Name = "My DB subnet group"
  }
}

resource "aws_db_instance" "db" {
  allocated_storage      = 10
  identifier             = "medcloud"
  db_name                = "medcloud"
  engine                 = "mysql"
  engine_version         = "5.7"
  instance_class         = "db.t3.micro"
  username               = "admin"
  password               = "12345678"
  parameter_group_name   = "default.mysql5.7"
  skip_final_snapshot    = true
  publicly_accessible    = true
  db_subnet_group_name   = aws_db_subnet_group.medcloud_db_group.name
  vpc_security_group_ids = [module.web_server_sg.security_group_id]
}

#Creating IAM User

resource "aws_iam_user" "files" {
  name = "files"
  path = "/"

  tags = {
    tag-key = "tag-value"
  }
}

resource "aws_iam_access_key" "files" {
  user = aws_iam_user.files.name
}

resource "aws_iam_user_policy" "files_ro" {
  name = "S3_Write_Read"
  user = aws_iam_user.files.name

  policy = <<EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:PutObject"
            ],
            "Resource": "${module.s3_bucket.arn}"
        }
    ]
}
EOF
}

# Creating ECR Repository

resource "aws_ecr_repository" "medcloud-image" {
  name                 = "medcloud"
  image_tag_mutability = "MUTABLE"

}


# Creating Roles

# Role for Task Execution
resource "aws_iam_role" "role_task" {
  name = "ecsTaskExecutionRole"
  assume_role_policy = jsonencode({
    "Version" : "2008-10-17",
    "Statement" : [
      {
        "Sid" : "",
        "Effect" : "Allow",
        "Principal" : {
          "Service" : "ecs-tasks.amazonaws.com"
        },
        "Action" : "sts:AssumeRole"
      }
    ]
  })
}

# Role for CodeBuild
resource "aws_iam_role" "role" {
  name = "buildRole"

  assume_role_policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": {
        "Service": "codebuild.amazonaws.com"
      },
      "Action": "sts:AssumeRole"
    }
  ]
}
EOF
}


# Role for Pipeline 
resource "aws_iam_role" "rolePipeline" {

  name = "PipelineMedCloud"
  path = "/service-role/"

  assume_role_policy = jsonencode({
    Version : "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
        Service = "codepipeline.amazonaws.com" }
      }
    ]
  })
}

# Policy for CodeBuild
resource "aws_iam_role_policy" "rolePolicy" {
  role = aws_iam_role.role.name

  policy = <<POLICY
{
    "Version": "2012-10-17",
    "Statement": [
        {
          "Effect": "Allow",
            "Action": [
                "ecr:GetAuthorizationToken",
                "ecr:BatchCheckLayerAvailability",
                "ecr:GetDownloadUrlForLayer",
                "ecr:GetRepositoryPolicy",
                "ecr:DescribeRepositories",
                "ecr:ListImages",
                "ecr:DescribeImages",
                "ecr:BatchGetImage",
                "ecr:GetLifecyclePolicy",
                "ecr:GetLifecyclePolicyPreview",
                "ecr:ListTagsForResource",
                "ecr:DescribeImageScanFindings",
                "ecr:InitiateLayerUpload",
                "ecr:UploadLayerPart",
                "ecr:CompleteLayerUpload",
                "ecr:PutImage"
            ],
            "Resource": "*"
        },
        {
            "Effect": "Allow",
            "Action": [

                "s3:GetObject",
                "s3:PutObject"

            ],
            "Resource": "*"
        }
    ]
}
POLICY
}

# CodeBuild

resource "aws_codebuild_project" "MedCloud-Build" {
  name         = "MedCloud-Build"
  service_role = aws_iam_role.role.arn

  artifacts {
    type = "NO_ARTIFACTS"
  }

  environment {

    compute_type                = "BUILD_GENERAL1_SMALL"
    image                       = "aws/codebuild/amazonlinux2-x86_64-standard:4.0"
    image_pull_credentials_type = "CODEBUILD"
    privileged_mode             = true
    type                        = "LINUX_CONTAINER"

  }

  source {
    type            = "GITHUB"
    location        = "https://github.com/weirygon/development-challenge-eight.git"
    git_clone_depth = 1

    git_submodules_config {
      fetch_submodules = false
    }

  }

  source_version = var.branch

}


# Creating Task Definition

resource "aws_ecs_task_definition" "tskDef-med" {

  family                   = "medcloudContainer"
  requires_compatibilities = ["FARGATE"]
  network_mode             = "awsvpc"
  cpu                      = 1024
  memory                   = 2048
  execution_role_arn       = aws_iam_role.role_task.arn

  container_definitions = jsonencode([
    {
      command     = []
      cpu         = 1024
      entryPoint  = []
      environment = []
      image       = aws_ecr_repository.medcloud-image.repository_url
      essential   = true

      memory            = 2048
      memoryReservation = 128
      mountPoints       = []
      name              = var.container_name
      portMappings = [
        {
          containerPort = 80
          hostPort      = 80
          protocol      = "tcp"
        },
      ]

    }
  ])

  runtime_platform {
    cpu_architecture        = "X86_64"
    operating_system_family = "LINUX"
  }

}

#Creating Cluster

resource "aws_ecs_cluster" "med_cluster" {

  name = "MedCloud-Cluster"

  setting {
    name  = "containerInsights"
    value = "enabled"
  }

}

resource "aws_ecs_cluster_capacity_providers" "provider" {
  cluster_name = aws_ecs_cluster.med_cluster.name

  capacity_providers = ["FARGATE", "FARGATE_SPOT"]

}

# Creating Load Balancer, Target Group e Listener

resource "aws_lb" "lb_med" {
  name               = "lb-medcloud"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [module.web_server_sg.security_group_id]
  subnets            = [for subnet in module.vpc.public_subnets : subnet]

  enable_deletion_protection = true

  tags = {
    Environment = "production"
  }
}

resource "aws_lb_target_group" "tg-med" {
  name        = "gr-tg-medcloud"
  port        = 80
  protocol    = "HTTP"
  target_type = "ip"
  vpc_id      = module.vpc.vpc_id

  health_check {
    matcher = "200,302"
  }

}

resource "aws_lb_listener" "listener" {

  load_balancer_arn = aws_lb.lb_med.arn
  port              = "80"
  protocol          = "HTTP"

  default_action {

    type             = "forward"
    target_group_arn = aws_lb_target_group.tg-med.arn

    fixed_response {
      content_type = "text/plain"
      message_body = "Fixed response content"
      status_code  = "200"
    }
  }
}

# Creating service 

resource "aws_ecs_service" "sv-med" {

  name            = "MedCloud-Service"
  cluster         = aws_ecs_cluster.med_cluster.id
  task_definition = data.aws_ecs_task_definition.tskDef-med.arn
  desired_count   = 1

  enable_ecs_managed_tags           = true
  health_check_grace_period_seconds = 0
  launch_type                       = "FARGATE"
  platform_version                  = "LATEST"
  propagate_tags                    = "NONE"

  wait_for_steady_state = false

  deployment_controller {
    type = "ECS"
  }

  load_balancer {
    container_name   = var.container_name
    container_port   = 80
    target_group_arn = aws_lb_target_group.tg-med.arn
  }

  network_configuration {
    assign_public_ip = true
    security_groups  = [module.web_server_sg.security_group_id]
    subnets          = module.vpc.public_subnets
  }

}

# Import Codestarconection

resource "aws_codestarconnections_connection" "test-connection" {

  name          = "undefined"
  provider_type = "GitHub"

}

# Creating pipeline

resource "aws_codepipeline" "pipeline" {
  name     = "MedCloud-Pipeline"
  role_arn = aws_iam_role.rolePipeline.arn

  artifact_store {
    location = var.bucket_name
    region   = ""
    type     = "S3"
  }

  stage {
    name = "Source"

    action {
      owner           = "AWS"
      provider        = "CodeStarSourceConnection"
      region          = var.aws_region
      role_arn        = ""
      run_order       = 1
      version         = "1"
      category        = "Source"
      input_artifacts = []
      name            = "Source"
      namespace       = "SourceVariables"
      output_artifacts = [
        "SourceArtifact",
      ]

      configuration = {
        BranchName           = var.branch
        ConnectionArn        = aws_codestarconnections_connection.test-connection.arn
        FullRepositoryId     = "weirygon/development-challenge-eight"
        OutputArtifactFormat = "CODE_ZIP"
      }
    }
  }

  stage {
    name = "Build"

    action {
      name      = "Build"
      category  = "Build"
      owner     = "AWS"
      provider  = "CodeBuild"
      region    = var.aws_region
      role_arn  = ""
      run_order = 1
      version   = "1"
      namespace = "BuildVariables"

      input_artifacts = [
        "SourceArtifact",
      ]

      output_artifacts = [
        "BuildArtifact",
      ]

      configuration = {
        ProjectName = aws_codebuild_project.MedCloud-Build.name
      }

    }
  }

  stage {
    name = "Deploy"

    action {

      name             = "Deploy"
      category         = "Deploy"
      namespace        = "DeployVariables"
      output_artifacts = []
      owner            = "AWS"
      provider         = "ECS"
      region           = var.aws_region
      role_arn         = ""
      run_order        = 1
      version          = "1"

      input_artifacts = [
        "BuildArtifact",
      ]

      configuration = {
        ClusterName = aws_ecs_cluster.med_cluster.name
        FileName    = "imagedefinitions.json"
        ServiceName = aws_ecs_service.sv-med.name
      }

    }

  }

}