<!DOCTYPE html>
<html>
<head>
    <title>All Users</title>
</head>
<body>
<h2>User List</h2>
<table border="1" width="100%" cellspacing="0">
    <tr>
        <th>Name</th>
        <th>Email</th>
    </tr>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
        </tr>
    @endforeach
</table>
</body>
</html>
