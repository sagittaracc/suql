<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .task {}
        .task.done {text-decoration: line-through;}
    </style>
</head>
<body>
    <?php
        use suql\frontend\html\view\View;
        require '../../../vendor/autoload.php';
    ?>
    <script src="../js/component.js"></script>
    <?=View::render(TodoList::class, [
        'todoList' => [
            ['id' => 1, 'todo' => 'do work', 'done' => true],
            ['id' => 2, 'todo' => 'sleep enough', 'done' => false],
        ],
    ])?>
</body>
</html>