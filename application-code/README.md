# [nginx.conf](./nginx.conf)
## location / – React Frontend
Serves static files from the React build folder. If a file isn't found, it falls back to index.html. This is essential for single-page applications (SPAs) like React, where routing is handled client-side.

## location ~ \.php$ – PHP App Behind Another NGINX
Handles requests for .php files by forwarding them to another internal NGINX server (which runs PHP-FPM).
You should replace APP_TIER_NGINX_IP_OR_PRIVATE_DNS with your actual private IP or hostname of the app server in your App Tier.

## location /api/ – Proxies API to Internal ALB
Captures any request that begins with /api/ and proxies it to the internal load balancer (ALB) in the App Tier.
Replace INTERNAL-ALB-DNS with your internal ALB DNS name or private IP (e.g., internal-myapp-alb-123456.elb.internal.amazonaws.com).
