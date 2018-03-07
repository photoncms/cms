<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: Verdana, Geneva, sans-serif;
                font-size: 12px;
            }
            .table_title {

            }
            .table_wrapper{
                text-align: center;
            }
            .table_wrapper * {
                text-align: left;
            }
            .universal_table {
                border: 1px solid #4a72a2;
                border-top: 3px solid #4a72a2;
                border-spacing: 0px;
                border-collapse: collapse;
                margin: auto;
                width: 100%;
            }
            .universal_table th {
                background-color: #e9eef6;
                border: 1px solid #4a72a2;
                font-weight: normal;
                padding: 5px;
            }
            .universal_table tr.odd {
                background: #f0f0f0;
            }
            .universal_table td {
                border-left: 1px solid #4a72a2;
                border-right: 1px solid #4a72a2;
                padding: 5px;
            }
        </style>
    </head>
    <body>
        <h2 class="table_title">Users</h2>
        <div class="table_wrapper">
            <table class="universal_table">
                <tr>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th>Confirmed</th>
                    <th>Roles</th>
                </tr>

                <?php
                    $rowCounter = 1;

                    foreach ($users as $user) :
                ?>
                
                    <tr @if ($rowCounter % 2 != 0) class="odd" @endif>
                        <td>{{ $user->first_name }}</td>
                        <td>{{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ($user->confirmed) ? 'Yes' : 'No' }}</td>
                        <td>
                            @if ($user->roles_relation)
                                <?php
                                    foreach ($user->roles_relation as $role) :
                                ?>
                                    {{ $role->title }} 
                                <?php
                                    endforeach;
                                ?>
                            @endif
                        </td>
                    </tr>
                    
                <?php
                    $rowCounter++;
                    endforeach;
                ?>
            </table>
        </div>
    </body>
</html>
