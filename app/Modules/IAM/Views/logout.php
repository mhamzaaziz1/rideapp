<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>
</head>
<body>
    <script>
        localStorage.removeItem('jwt_token');
        window.location.href = '<?= base_url() ?>';
    </script>
</body>
</html>
