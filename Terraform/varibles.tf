variable "aws_region" {
  type        = string
  default     = "us-east-1"
  description = ""
}

variable "branch" {
  type        = string
  default     = "production"
  description = "The branch of GitHub Project"
}

variable "container_name" {
  type    = string
  default = "MedCloudContainer"

}

variable "bucket_name" {
  type    = string
  default = "medcloud-file"

}
