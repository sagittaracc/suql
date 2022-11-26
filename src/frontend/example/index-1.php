<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../../vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #489cc1;
        }

        .todo-form {
            width: 580px;
            background-color: rgb(252, 252, 252);
        }

        .todo-form h1 {
            color: rgb(84, 84, 84);
        }
    
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
            ['id' => 2, 'todo' => 'sleep enough', 'done' => true],
            ['id' => 3, 'todo' => 'wake up early', 'done' => false],
        ],
    ])?>
    <script src="../../../vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>