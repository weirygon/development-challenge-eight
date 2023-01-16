# DATABASE
output "db_url" {
  description = "The URL database"
  value       = aws_db_instance.db.endpoint
}

# IAM
output "iam_user_name" {
  description = "The user's name"
  value       = aws_iam_user.files.name
}

output "iam_user_access_key_id" {

  description = "The access key from user"
  value       = aws_iam_access_key.files.id

}

output "iam_user_access_key_secret" {
  description = "The access key from user"
  value       = aws_iam_access_key.files.secret
  sensitive   = true
}

#ECR

output "ecr_uri" {
  description = ""
  value       = aws_ecr_repository.medcloud-image.repository_url
}

#Load Balancer

output "load_balancer_url" {
  value = aws_lb.lb_med.dns_name
}
