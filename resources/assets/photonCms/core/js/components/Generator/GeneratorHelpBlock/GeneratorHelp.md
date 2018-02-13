# Photon CMS Admin Panel - Generator

## Module Creation - `create`

### About the Generator

The generator is used to set up the main system functionality. You can CRUD modules and their fields through the generator. Keep in mind that some actions may impact your current storage data, so be sure to **back up if you are removing some fields/modules**.

Photon Generator uses a built-in transaction controller. Handling of all requests is performed through transactions and in an event of a failure, the transaction is rolled back. In transactions which impact data in tables (for example removing a field), data will still be affected so **be sure to back up your data before using the generator**!

### Module Types

| Type                | Description                                                                                                                                                              |
|---------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Single Entry        | Can have only one entry. If the entry hasn’t been created yet, it will be automatically created. The entry cannot be deleted. Suitable for custom configuration options. |
| Non Sortable        | Regular module with no sorting options.                                                                                                                                  |
| Sortable            | Entries can be sorted and nested within module entries.                                                                                                                  |
| Multilevel Sortable | Entries can be sorted and nested within module entries and entries of another module. Suitable for products and such which should be nested within categories.           |

{: .table-bordered .table-striped .table-hover .generator-docs-table}


 `Sortable` and `Multilevel Sortable` modules can be scoped within entries of a parent module.


### Module Options

| Parameter    | Required | Type    | Description                                                                                                                                                                          |
|--------------|----------|---------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Anchor Text  | No       | string  | Stub which will be used to generate human-readable text for each entry representation (explained in Anchor Texts section)                                                            |
| Category     | No       | int     | Module ID of a parent module. If this value is set then each entry of the current module can belong to an entry of the parent module (scope).                                        |
| Icon         | No       | string  | Generic icon name used in Photon CMS frontend.                                                                                                                                       |
| Is System    | No       | boolean | (Deprecated) Indicates if the module is a system module and if it should be preserved on Photon Reset.                                                                               |
| Lazy Loading | No       | boolean | Indicates if module entries should be loaded in paginated fashion rather than in one large chunk (e.g. in relational dropdowns).                                                     |
| Max Depth    | No       | int     | Maximum nesting depth.                                                                                                                                                               |
| Name         | Yes      | string  | Name of the module.                                                                                                                                                                  |
| Reporting    | No       | boolean | Indicates if operations over this module (CRUD-ing entries) will be considered as reporting only. If set to true, to save an entry, you would need to provide a positive force flag. |
| Slug         | No       | string  | Stub which will be used to generate url-friendly text for each entry representation (similar to Anchor Text).                                                                        |
| Table Name   | Yes      | string  | Table name for the module in snake_case notation.                                                                                                                                    |
| Type         | Yes      | string  | Module type (one of the values from Module Types response).                                                                                                                          |

{: .table-bordered .table-striped .table-hover .generator-docs-table}

### Fields Options

#### Field Attributes

Each field has attributes according to its purpose. Following is the list in alphabetic order of all available field attributes.

| Name                     | Type    | Required | Editable | Default                              |
|--------------------------|---------|----------|----------|--------------------------------------|
| Can Create Search Choice | boolean | No       | Yes      | false                                |
| Column Name              | string  | Yes      | No       | -                                    |
| Default                  | mixed   | No       | Yes      | null                                 |
| Disabled                 | boolean | No       | Yes      | false                                |
| Editable                 | boolean | No       | Yes      | true                                 |
| Flatten to Optgroups     | boolean | No       | Yes      | false                                |
| Foreign Key              | mixed   | No       | Yes      | automatic assignment if not supplied |
| Hidden                   | boolean | No       | Yes      | false                                |
| Indexed                  | boolean | No       | Yes      | false                                |
| Is Default Search Choice | boolean | No       | Yes      | false                                |
| Is System                | boolean | No       | Yes      | false                                |
| Lazy Loading             | boolean | No       | Yes      | false                                |
| Local Key                | mixed   | No       | Yes      | automatic assignment if not supplied |
| Name                     | string  | Yes      | Yes      | -                                    |
| Nullable                 | boolean | No       | Yes      | false                                |
| Pivot Table              | string  | No       | No       | automatic assignment if not supplied |
| Related Module           | string  | Yes      | No       | -                                    |
| Relation Name            | string  | Yes      | No       | -                                    |
| Is Active Entry Filter   | string  | No       | Yes      | null                                 |
| Tooltip Text             | string  | No       | Yes      | empty string                         |
| Type                     | int     | Yes      | No       | -                                    |
| Validation Rules        | string  | No       | Yes      | null                                 |

{: .table-bordered .table-striped .table-hover .generator-docs-table}

#### Field Attributes Descriptions

| Name                     | Description                                                                                                                                                                                                                                                        |
|--------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Can Create Search Choice | Use to determine if this field can trigger the creation of search choice from select2.js plugin.                                                                                                                                                                   |
| Column Name              | Name of the DB column for the field in snake_case notation.                                                                                                                                                                                                        |
| Default                  | Sets the default parameter in the DB.                                                                                                                                                                                                                              |
| Disabled                 | Use this to determine if the field in the form should be disabled.                                                                                                                                                                                                 |
| Editable                 | Use this to determine if the field in the form should be editable.                                                                                                                                                                                                 |
| Flatten To Optgroups     | If set to true, select2.js plugin will flatten all entries fetched from multilevel sortable module, and use the root level as optgorups, and everything below as options.                                                                                          |
| Foreign Key              | Custom foreign key in snake_case notation that will be used for relation. If the value is not provided, it will be auto-generated. In case of One to many field user must provide custom foreign_key in order for relation within module to be properly generated. |
| Hidden                   | Use this to determine if the field in the form should be hidden.                                                                                                                                                                                                   |
| Indexed                  | Adds/removes the field indexing in the DB.                                                                                                                                                                                                                         |
| Is Default Search Choice | Use to determine which field should receive the data created via select2.js plugin.                                                                                                                                                                                |
| Is System                | Use this to determine if the field in the form should be only read and assigned by the system.                                                                                                                                                                     |
| Lazy Loading             | Use this to determine if values should be lazy-loaded.                                                                                                                                                                                                             |
| Local Key                | Custom local key in snake_case notation that will be used for relation. If the value is not provided, it will be auto-generated.                                                                                                                                   |
| Module Id                | ID of the module to which the field belongs (this is automatically assigned and is a read-only value).                                                                                                                                                             |
| Name                     | Human readable field name.                                                                                                                                                                                                                                         |
| Nullable                 | Sets the nullable parameter in the DB.                                                                                                                                                                                                                             |
| Pivot Table              | Name of the pivot table in snake_case notation (if the field represents a many-to-many relation). If the value is not provided, it will be auto-generated.                                                                                                         |
| Related Module           | ID of a related module (if the field represents a relation).                                                                                                                                                                                                       |
| Relation Name            | Name of the relation in snake_case notation (if the field represents a relation).                                                                                                                                                                                  |
| Is Active Entry Filter   | Name of field from related module that is used for filtering search choices for select2.js plugin.                                                                                                                                                                 |
| Tooltip Text             | Text which should be used in front end to implement a popup over a field with field explanation.                                                                                                                                                                   |
| Type                     | ID of the field type. Explained in the Field Types section.                                                                                                                                                                                                        |
| Validation Rules         | Validation rules written using Laravel validation notation.                                                                                                                                                                                                        |
| Virtual Name             | Name of the virtual field in snake_case notation (if the field is virtual).                                                                                                                                                                                        |

{: .table-bordered .table-striped .table-hover .generator-docs-table}

### Anchor Text

Anchor texts are used to visually represent each entry. For example, for a user, it can be the user first and last name. Module anchor_text field can contain any text and also supports shortcodes for fields.

For example, if anchor text is `User: {{first_name}} {{last_name}}` that means that each entry within that module will have its anchor text compiled by inserting values from `first_name` and `last_name` columns of the respective entry. Like `User: John Doe`.

Many-to-one relations can also be used inside `anchor_text` field. In this case, the shortcode would be the name of the related table name of the field. For example, if your relation is a field named ‘title’ in a table named ‘companies’, then your shortcode would be `{{companies.title}}`. An example of a whole anchor text of a user would be `{{first_name}} {{last_name}}, {{companies.title}}` and would render to something like `John Doe, Photon CMS Inc.`.

Anchor texts are used by our native frontend for searching within entires of individual dynamic module. They can also be used within API filter route as any other module field.

### Anchor Html

In case you need to implemenet advanced visual representation you can use anchor html. Within it you can store any html code together with shortcodes representing module fields, used in a same way as for anchor text.

If anchor html is defined it will be used for visual representation within our native frontend app. But anchor text will still be used by default for searching.

You can also define your custom method that would be used instead of shortcode representation of fields. In order to use it you need to define your own trait and use it within `/app/PhotonCms/Core/Entities/DynamicModule/DynamicModuleHelpers.php`. Custom method shortcode should be written in this format: `{{argument1,argument2,...,argumentN|methodName}}` where arguments are name of the fields. For example method shortcode `{{width,height|max}}` will call method max from your custom trait, pass fields width and height from individual dynamic module entry, determine which field is larger and print its value within anchor html.


