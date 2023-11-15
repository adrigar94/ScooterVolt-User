[![Test](https://github.com/adrigar94/ScooterVolt-User/actions/workflows/test.yml/badge.svg)](https://github.com/adrigar94/ScooterVolt-User/actions/workflows/test.yml)

# ScooterVolt-User
The User service is a microservice in the ScooterVolt platform, responsible for user registration, authentication, and authorization. This service is built using Symfony.

## API Reference

Is avaible in ```https://localhost:8000/api/doc```

## Requirements before running API
- Have the database with the migrations executed
- Having generated the key pair for JWT

## Testing

To run all tests:
```composer test```

To run unit tests:
```composer test-unit```

To run integration tests:
```composer test-integration```


## TODO
- [ ] Create object mother for tests


## Code quality
- vendor/bin/rector process --ansi --dry-run
- vendor/bin/ecs check