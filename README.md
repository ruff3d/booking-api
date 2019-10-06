# booking-api

App for booking a meeting room

# initial instalation
`make init`

# Available commands with `make`

		init        - Init command for freash setup
		migration   - Perform the migrations
		start       - Build Images, run container
		stop        - Stop and remove running containers
		sh          - Log in to php cntainer
		restart     - Restart all containers
		test        - Run tests
		
# ApiDoc
served on http://localhost:8080/v2/doc

# Types of resouces
http://localhost:8080 - NGINX - host resource without cache
http://localhost:8181 - Varnish - Reverse cache proxy 
