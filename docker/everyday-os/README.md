# Everyday OS - Mixpost Docker Configuration

This directory contains the Docker configuration for running Mixpost as part of the Everyday OS stack.

## Setup

1. Copy the `.env.example` file to `.env` and fill in your configuration:
   ```bash
   cp .env.example .env
   ```

2. Update the environment variables in `.env` with your actual values:
   - MySQL passwords
   - Email configuration
   - SSL email for Let's Encrypt

3. Run the Docker Compose stack:
   ```bash
   docker-compose up -d
   ```

## Configuration

The docker-compose.yml file includes:
- **mixpost-mysql**: MySQL 8.0 database server
- **mixpost**: Mixpost Pro Team Edition application

## Volumes

- `mixpost_storage`: Persistent storage for Mixpost application files
- `mysql_data`: MySQL database files

## Ports

- `9000`: Mixpost web interface (mapped to container port 80)

## License

The LICENSE_KEY in the docker-compose.yml is specific to the Everyday OS deployment. 
Please update it with your own license key if deploying elsewhere.

## Integration with Everyday OS

This configuration is designed to work with the broader Everyday OS ecosystem, including:
- Valkey (Redis) for caching and queues
- Shared networking with other services
- Centralized SSL termination via Caddy