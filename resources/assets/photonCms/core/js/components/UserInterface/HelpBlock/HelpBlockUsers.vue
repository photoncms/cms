<template>
    <div class="panel-group" id="demo-accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#demo-accordion" href="#demo-collapseOne">
                        USER MANAGEMENT TIPS
                    </a>
                </h4>
            </div>
            <div id="demo-collapseOne" class="panel-collapse collapse in">
                <div class="panel-body">
                    <h5 class="section-title">How to impersonate a user?</h5>
                    <ul>
                        <li>Select a user from a list in the tree menu</li>
                        <li>Click the "Impersonate User" button in the form action bar</li>
                    </ul>
                    <br>

                    <h5 class="section-title">How to stop impersonation?</h5>
                    <ul>
                        <li>Click the 'Stop impersonating user' menu option in the bottom left corner  of the screen</li>
                        <li>If your navigating the Users module page on a mobile device, you can access the 'Stop impersonating user' menu option in the mobile menu from the page header.</li>
                    </ul>
                    <br>

                    <h5 class="section-title">How can I set the new password?</h5>
                    The initial set of password policy options requires that your password must be:
                    <br>
                    <br>
                    <ul>
                        <li>At least 8 characters in length</li>
                        <li>Containing at least one alpha, one numeric, and one special character</li>
                        <li>Not reused for a history of 13 passwords</li>
                    </ul>
                    Note that your password is set to exipre after 90 days.
                    Expiring after 90 days
                    <br>
                    <br>

                    <h5 class="section-title">How can I change my email address?</h5>
                    You can change the email address by typing a new one in the Email input field. After you click the 'Save Changes' button the following process takes place:
                    <br>
                    <br>
                    <ul>
                        <li>An email is sent to a new email address asking you to confirm it.</li>
                        <li>Once you click the confirmation link the email address is updated</li>
                    </ul>
                    If you don't complete the process above, you'll still be able to log-in using your old email address.
                    <br>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#demo-accordion" href="#demo-collapseTwo">
                        GENERAL MODULE EDITING TIPS
                    </a>
                </h4>
            </div>
            <div id="demo-collapseTwo" class="panel-collapse collapse">
                <div class="panel-body">
                    1. Module Name (Input Text)<br><br>

                    Tooltip text: "Module name as it will appear in admin panel menu, as module page title etc.”
                    Typing a module name automatically generates valid MYSQL Table Name (lowercase, snakecase)<br><br>

                    2. Table Name (Input Text)<br><br>

                    Tooltip text: "Auto-generated MYSQL table name that can be changed by typing in a new value.”
                    Automatically generated (though it can be changed manually)
                    Renaming a table is not possible<br><br>

                    3. Category (Select Menu)<br><br>

                    Visible only for Simple Sortable Module and Multilevel Sortable Module
                    Select menu lists only Simple Sortable and Multilevel Sortable Modules
                    Tooltip text: "If selected, current module will be nestable under selected category module's entries."
                    Note: If a module group is selected in the tree menu, the module will be created under that module group.
                </div>
            </div>
        </div>
    </div>
</template>
