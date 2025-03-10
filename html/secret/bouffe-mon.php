<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $code = $_POST['code'] ?? '';
    if ($code) {
        if (file_put_contents('/dynlan.txt', date('c') . PHP_EOL . $code . PHP_EOL, FILE_APPEND) === false) {
            echo 'je vais te retrouver';
        }
        /* ?><pre><samp>
        <?php try {
            $return_value = eval($code) ?></samp></pre>
            <?php if ($return_value !== null) { ?>
                <p>Return value</p>
                <pre><samp><?= $return_value ?></samp></pre>
            <?php }
        } catch (Throwable $e) { ?>
            </samp></pre><p>Exception</p>
            <pre><samp><?= strval($e) ?></samp></pre>
        <?php } ?>
        <hr>
    <?php */
    }
    ?>
    <form method="post">
        <p><label for="code">Code</label></p>
        <p><textarea name="code" id="code" rows="23" cols="120"><?= $code ?></textarea></p>
        <p><button type="submit">Exécuter</button></p>
    </form>
</body>

</html>