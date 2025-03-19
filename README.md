## ES Lib Bundle
ES Lib Bundle is a Symfony bundle that brings the power of the es-lib event-sourcing library into your Symfony applications. It simplifies the integration of event-sourcing by leveraging Symfony’s dependency injection, configuration system, and Doctrine ORM for event storage. Whether you're building scalable systems or need a reliable audit trail, this bundle makes it easy to implement event-sourcing patterns.

### Features
- Event Sourcing Made Simple: Integrates es-lib for managing aggregates, events, and repositories.
- Dependency Injection: Automatically wires up es-lib services and bundle-specific services for seamless use.
- Event Storage: Supports Doctrine ORM with configurable drivers (e.g., SQLite, MySQL) for persisting events.
- Aggregate Management: Provides pre-configured repositories to load and save aggregates effortlessly.
- Flexible Configuration: Customize the bundle to fit your application’s database and event store needs.

### Requirements
- PHP 8.3 or higher
- Symfony 7.2 or higher
- Doctrine ORM (installed via symfony/orm-pack)

### Installation
Install the bundle using Composer:
```php
composer require awd-studio/es-lib-bundle
```

### Enable the Bundle
Register the bundle in your Symfony application by adding it to `config/bundles.php`:
```php
return [
    // Other bundles...
    AwdStudio\EsLibBundle\AwdEsBundle::class => ['all' => true],
];
```

### Support
For help or to report issues, visit the [GitHub repository](https://github.com/awd-studio/es-lib-bundle) and open an issue.

### License
This bundle is released under the MIT License. See the [LICENSE](LICENSE) file for more information.
