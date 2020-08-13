Symfony bundle to add basic support for DataTables

If mapping is required add the following file:
config/packages/data_tables_compatibility.yaml

```yaml
data_tables_compatibility:
  mapping:
    - from: username
      to: name
```
It is based on regex, so if you change "pie" and you have the values "apple pie" and "cherry pie" both will be replaced.
