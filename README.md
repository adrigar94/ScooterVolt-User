# ScooterVolt-User
The User service is a microservice in the ScooterVolt platform, responsible for user registration, authentication, and authorization. This service is built using Symfony.

## API Reference

Is avaible in ```https://localhost:8000/api/doc```

## Testing

To run all tests:
```composer test```

To run unit tests:
```composer test-unit```

To run integration tests:
```composer test-integration```


## TODO
- [ ] Create object mother for tests
- [ ] Implement Domain Events
- [ ] Remove JWT_PASSPHRASE from .env, move to file... and renew JWT_PASSPHRASE