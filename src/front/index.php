<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php require_once 'Each.php'; ?>
    <?php require_once 'Value.php'; ?>
    <?php require_once 'Element.php'; ?>
    <?php require_once 'Input.php'; ?>
    <?php require_once 'Button.php'; ?>
    <?php require_once 'View.php'; ?>
    <?php require_once 'Scope.php'; ?>
    <?php require_once 'Component.php'; ?>
    <?php require_once 'HelloMessage.php'; ?>
    <?php require_once 'CountComponent.php'; ?>
    <script src="component.js"></script>

    <?=View::render(HelloMessage::class, ['name' => 'Mike'])?>
</body>
</html>