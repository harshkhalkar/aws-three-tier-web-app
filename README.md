# AWS Three Tier Web Architecture

## Overview

[](./Multi Tier Architecture diagram (1).png)
In this architecture, a public-facing Application Load Balancer forwards client traffic to our web tier EC2 instances. The web tier is running Nginx webservers that are configured to serve a simple HTML website and redirects our .php calls to the application tier’s internal facing load balancer. The internal facing load balancer then forwards that traffic to the application tier, which is written in PHP.

## VPC and Subnet Design

A Virtual Private Cloud (VPC) was created with the following configuration:

- DNS support and DNS hostnames are enabled.
- The VPC CIDR block is 10.0.0.0/16.
- The VPC contains:
  - 2 Public Subnets across 2 Availability Zones.
    - These subnets have `MapPublicIpOnLaunch` set to true to allow instances to automatically receive public IPs.
  - 6 Private Subnets distributed across 2 Availability Zones.
    - These subnets have `MapPublicIpOnLaunch` set to false to isolate internal services.

An Internet Gateway was created and attached to the VPC. A public route table was also created with a default route to the Internet Gateway. Both public subnets were associated with this route table.

## Security Groups

Security groups were configured to follow the principle of least privilege:

- Application Tier SG: Allows traffic only from the Web Tier.
- Web Tier SG: Allows HTTP/HTTPS from the internet and forwards traffic to the Application Tier.
- Database Tier SG: Allows MySQL (port 3306) traffic only from the Application Tier.
- Application Load Balancer: Allows Public Access and then forwards their traffic to Web Tier.

## RDS Configuration

An RDS MySQL database was deployed in the private subnets using a dedicated DB subnet group.

Asynchronous replication is available in RDS, allowing for read replicas and failover scenarios if needed.

### Choosing Between RDS and DynamoDB

- Use **RDS** when:
  - We need relational data modeling and complex queries (joins, foreign keys).
  - We want to manage a traditional SQL-based backend.
  - We require managed backups, Multi-AZ deployment, and transaction support.

- Use **DynamoDB** when:
  - We need high throughput with low-latency access to key-value or document-based data.
  - We want to scale without managing servers or database patching.
  - We need schema flexibility and predictable performance at scale.

## EC2 and AMI Preparation

Two EC2 instances were launched in the public subnets for development purposes. After manual setup and configuration:

- A custom AMI was created from the prepared and tested instances.
- The AMI serves as the base image for Auto Scaling Groups in the Web and App tiers.

Although these EC2 instances were initially in public subnets, this setup can be improved by using a NAT Gateway or Bastion Host to restrict direct internet access.

## Auto Scaling and Launch Templates

Launch templates were created for both Web and App tiers using the custom AMIs. These templates were used to create Auto Scaling Groups that can scale in/out based on demand.

## Load Balancers

Two Application Load Balancers (ALBs) were created:

- An **Internal ALB** for the Application Tier.
  - Placed in private subnets.
  - Receives traffic from the Web Tier and routes it to App Tier EC2 instances.

- An **Internet-facing ALB** for the Web Tier.
  - Placed in public subnets.
  - Receives traffic from users and forwards it to the Web Tier EC2 instances.
  - The DNS of this ALB is used to access the application externally.

## Tier Isolation and Design

Each tier of the architecture is placed in the appropriate subnet type to maintain a strong security boundary:

- Database Tier (RDS): Private Subnets
- Application Tier (App EC2s): Private Subnets
- Web Tier (Web EC2s): Private Subnets
- Internet-facing ALB: Public Subnets
- Internal ALB: Private Subnets

## Best Practices Followed

- Separated subnets by tier for better isolation and security.
- Enabled public IP mapping only for public subnets.
- Used security groups to enforce tier-to-tier communication rules.
- Created and used custom AMIs for consistency in EC2 configurations.
- Deployed applications behind ALBs for scalability and availability.
- Used Auto Scaling Groups with proper launch templates for automation.
- Deployed RDS in private subnets and restricted access using security groups.
- Maintained a clear separation of concerns across Web, App, and DB tiers.

## Possible Improvements

- Replace initial public EC2 instances with a Bastion Host or Systems Manager Session Manager for secure access.
- Use a NAT Gateway for internet-bound traffic from private instances.
- Enable Multi-AZ deployment for RDS to improve availability.
- Add CloudWatch Alarms to monitor performance and scale automatically.
- Integrate AWS WAF with the public ALB to protect against web exploits.
- Enable logging on the ALBs for audit and troubleshooting.
- Use Route 53 to assign a friendly domain name to the public ALB.

## Conclusion

This project successfully demonstrates the creation of a secure, scalable, and well-structured AWS three-tier architecture. The setup ensures isolation between tiers, automation through Auto Scaling Groups and Launch Templates, and secure access controls via security groups and subnet configuration.
