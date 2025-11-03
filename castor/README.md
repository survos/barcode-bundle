```
skeleton/
├── castor.php
├── README.md
└── demo/
├── src/
│   ├── Command/
│   │   └── ImportProductsCommand.php
│   └── Entity/
│       └── Product.php
└── templates/
└── products/
└── index.html.twig
```

**Rationale:**
- `.castor/` (hidden) keeps it out of the way but discoverable
- OR `castor/` if you want it more visible (your original choice works fine)
- `demo/` subdirectory makes it clear these are demo/scaffold artifacts, not part of the bundle itself

## Alternative: Consider "skeleton" terminology

Since you mentioned needing a better word than "artifacts/inputs", you could use Symfony's familiar terminology:
```
castor/
├── castor.php
├── README.md
└── skeleton/
├── src/
├── templates/
└── config/  # if you need demo config
```

This mirrors how Symfony uses "skeleton" for boilerplate code.

## Additional considerations

1. **Add a config directory** if your demo needs any YAML configuration (routes, services, etc.)

2. **Consider a .gitattributes** entry to exclude castor files from bundle distribution:
```
/castor export-ignore
