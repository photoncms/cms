# Photon CMS Admin Panel

## Invitations `invitations`

### About Invitations ###

Use the Invitations module to invite users to join your project. 

Upon receiving an email, invitees will be able to click a link and fill-in only their first name, last name and a password. Right after submitting the form they will be logged in, without a need to verify their email addresses.

### Creating Invitations ###

To create an invitation simply fill-in the invitee's email address and pre-fill any of the available fields. Assigned values will be automatically assigned to a user once he registers.

### Creating Multiple Invitations ###

To batch-invite users you can upload a CSV file containing email addresses of invitees.

Download a template CSV file here: [mass-invite-example.csv](/files/mass-invite-example.csv).

## Image Sizes `image_sizes`

### About Image Sizes ###

After you add a new image asset using the Asset Manager, an image is resized to a number of different image sizes, as specified here, in the Image Sizes Module.

### Defaut Image Sizes ###

Photon CMS has two initial image sizes:

**1. Thumbnail (120x90)**

This image size is used in Photon CMS Admin Panel, and you're advised not to remove it or alter it in any way.

**2. Large Image (960xauto)**

This image size is not used anywhere by the Photon CMS system, so you're free to modify it as you wish.

*'960xauto'* means that whatever dimensions uploaded image has, it will be scaled down horizontaly to 960px, and the height will be adjusted proportionally.

### Creating Image Sizes ###

#### Name field ####

Enter a human-readable name in the *Name* field.

#### Width and Height ####

If you set both width and hight values, the image will be cropped to exact dimensions, though no image skewing will occur. Some image cropping might occur, though, if the uploaded image has different proportions than those specified here.

Leaving one of the values set to 0 would have the effect of scaling the image down vertically or horizontaly using the non-zero value, and adjusting the vertical or horizontal edge (value set to zero) proportionally.

#### Lock Width and Height ####

Locking width and height would prevent you from setting a different aspect ratio when using the Asset Manager Image Sizes cropping tool.

## Permissions `permissions`

### About Permissions ###

Permissions are usually applied by assigning them to roles and then assigning roles to users. However, permissions can be directly assigned to users, as well.

Permissions are divided into two main semantic types: **permissions** and **restrictions**.

### Creating Permissions ###

Probably the easiest way to create permissions is to use the Permission Generator tool, though you can type-in the permission rule directly in *System Name* and *Title* fields.

Creating a permission will clear the cache afterwards.

Permissions names shoulf be generated following these patterns:

- `assign_role:[role name']` *(permits)*
- `revoke_role:[role name']` *(permits)*
- `retrieve_role:[role name']` *(permits)*
- `modify_module:[table name']` *(permits)*
- `retrieve_all_entries:[table name']` *(permits)*
- `[action']_module:[target table name']_match:[users table column name']_to:[target table column - name']..._[users table column name']_to:[target table column name']` *(restricts)*
- `[action']_module:[target table name']_match:[users table column name']_in:[related table name']:[related column name']..._[column name']_in:[relation field']_field:[related module column name']` *(restricts)*
- `cannot_edit_field:[table name']:[column name']` *(restricts)*
- `[action']_entry:[table name']` *(permits)*

**Valid actions names are:**

- `create`
- `retrieve`
- `update`
- `delete`

As previously mentioned, permissions can permit or restrict something. They will act as restrictions if assigned to a role.

For each permission which permits something, if it exists that means you must have it in order to perform that action. If a permission doesn't exist, that means that action doesn't require a permission and is globally available.

*Example 1: *

You have created a new module with a table name *'videos'*. Since you haven't created any permissions for it, anyone can CRUD module entries. If you create a permission named *'modify_module_videos'* that means that only users with this permission can now CRUD module entries.

*Example 2: *

For the previous example, you want to permit creating videos to only specific users. You create a new permission *'create_entry_videos'* and assign it to a user. Additionally if you have a permission *'modify_module_videos'* like in the previous example, then the user must have this permission too.

### About Automatic Permissions Logic ###

#### CREATE: ####

Q: Can you modify the module?

A: Yes if the permission doesn't exist, or it exists and you have it.

Permission: `modify_module_[table name]`

Q: Can you create module entries?

A: Yes if the permission doesn't exist, or it exists and you have it.

Permission: `create_entry_[table name]`

#### RETRIEVE: ####

Q: Can you retrieve all entries? 

A: Yes if the permission exists and you have it.

Permission: `retrieve_all_entries_[table name]`

Q: Can you retrieve a specific entry? 

A: Yes if the permission doesn't exist, or you have it and matches the criteria.

Permission: `retrieve_module_[table name]_match_[column name]_to_[column name]..._[column name]_to_[column name]`

#### UPDATE: ####

Q: Can you update desired fields?

A: Yes if the permission doesn't exist, or it exists but you don't have it, because this is a direct restriction.

Permission: `cannot_edit_field_[table name]_[field name]`

Q: Can you access the module? 

A: Yes if permission doesn't exist, or it exists and you have it.

Permission: `modify_module_[table name]`

Q: Can you update module entries?

A: Yes if the permission doesn't exist, or it exists and you have it.

Permission: `update_entry_[table name]`

Q: Can you update a specific entry?

A: Yes if the permission doesn't exist, or you have it and matches the criteria.

Permission: `update_module_[table name]_match_[column name]_to_[column name]..._[column name]_to_[column name]`

#### DELETE: ####

Q: Can you access the module?

A: Yes if permission doesn't exist, or it exists and you have it.

Permission: `modify_module_[table name]`

Q: Can you delete module entries?

A: Yes if the permission doesn't exist, or it exists and you have it.

Permission: `delete_entry_[table name]`

Q: Can you delete a specific entry?

A: Yes if the permission doesn't exist, or you have it and matches the criteria.

Permission: `delete_module_[table name]_match_[column name]_to_[column name]..._[column name]_to_[column name]`

### About Automatic Role Permissions Logic ###

The logic is applied through the Users Module Update method.

#### ASSIGN: ####

Q: Can you assign the role?

A: Yes if the permission doesn't exist, or it exists and you have it.

Permission: `assign_role_[role name]`

#### REVOKE: ####

Q: Can you revoke the role? 

A: Yes if the permission doesn't exist, or it exists and you have it.

Permission: `revoke_role_[role name]`

#### RETRIEVE: ####

Q: Can you retrieve the role?

A: Yes if the permission doesn't exist, or it exists and you have it.

Permission: `retrieve_role_[role name]`

## Roles `roles`

### About Roles ###

Roles *(also known as User Groups)* allow you to organize your system's user accounts. Each role can also be assigned with a certain set of permissions that are automatically transfered upon all users that are assigned with a given role.

One user can have more than one role, and if that is the case, rules assigned for any of the roles are additive.

To assign a role to a user locate a user in the [Users Module](/admin/users) and set a Role using the multiple selection *Roles* field.

### Creating Roles ###

To create a role fill-in the *System Name* and *Title* fields. 

System Name value will be used in the API returns, and the Title field will be used as a human-readable role name.

Creating a role will clear the permissions cache afterwards.

### Assigning Permission to Roles ###

You can check or uncheck any of the existing permission or restriction rules that you want to be applied to the selected role. 

If you don't see the required permission or restriction rule, you can use the Permission Generator tool to create a new rule.

Remember to save the Role entry to apply the selection.

Modifying a role will clear the permissions cache afterwards.

## File Tags `file_tags`

### About File Tags ###

You can use file tags to tag uploaded assets, so that it's easier to find them later on in the Asset Manager. Although not required by default, you are encouraged to tag uploded assets with a minimum of three tags.

If you'd like to make the file tags a required field, and enforce the minimum-three-tags rule, head over to the [Generator](/cp/generator) section (super-administrator role required), and set the following validation rule for the 'File Tags' field under the Assets Module: `exists:file_tags,id|required|min:3`

## Users Module `users`

### Setting a Password

#### How to Set a New Password?

The initial set of password policy options requires that your password must be:

* At least 8 characters in length
* Containing at least one alpha, one numeric, and one special character
* Not reused for a history of 13 passwords

Note that your password is set to exipre after 90 days.

### Changing Email Address

#### How to Change Email Address?

You can change the email address by typing a new one in the Email input field. After you click the 'Save Changes' button the following process takes place:

* An email is sent to a new email address asking you to confirm it.
* Once you click the confirmation link the email address is updated

If you don't complete the process above, you'll still be able to log-in using your old email address.

### About Roles ###

Roles are also known as *User Groups*.

One user can have more than one role, and if that is the case, permission rules assigned for any of the roles are additive.

To set-up roles go to the [Roles Module](/cp/admin/roles) (requires super-administrator role).

### About Permissions ###

Permissions and restrictions are usually applied using Roles, though you can apply permissions and restrictions directly to a user, too.

To set-up permissions go to the [Permissions Module](/cp/admin/permissions)

### User Impersonation 

#### How to Impersonate a User

* Select a user from a list in the tree menu.
* Click the "Impersonate User" button in the form action bar

#### How to Stop Impersonation?

* Click the 'Stop impersonating user' menu option in the bottom left corner  of the screen.
* If your navigating the Users module page on a mobile device, you can access the 'Stop impersonating user' menu option in the mobile menu from the page header.
