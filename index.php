<?php

$path = 'd';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = isset($_POST['text']) ? $_POST['text'] : file_get_contents("php://input");
    file_put_contents($path, $text);
    die;
}

if (isset($_GET['raw']) || strpos($_SERVER['HTTP_USER_AGENT'], 'curl') === 0 || strpos($_SERVER['HTTP_USER_AGENT'], 'Wget') === 0) {
    if (is_file($path)) {
        header('Content-type: text/plain');
        readfile($path);
    } else {
        header('HTTP/1.0 404 Not Found');
    }
    die;
}
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body {
    margin: 0;
    background: #ebeef1;
}
.container {
    position: absolute;
    top: 20px;
    right: 20px;
    bottom: 20px;
    left: 20px;
}
#content {
    margin: 0;
    padding: 20px;
    overflow-y: auto;
    resize: none;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    border: 1px solid #ddd;
    outline: none;
}
#printable {
    display: none;
}
@media (prefers-color-scheme: dark) {
    body {
        background: #333b4d;
    }
    #content {
        background: #24262b;
        color: #fff;
        border-color: #495265;
    }
}
@media print {
    .container {
        display: none;
    }
    #printable {
        display: block;
        white-space: pre-wrap;
        word-break: break-word;
    }
}
</style>
</head>
<body>
<div class="container">
<textarea id="content"><?php
if (is_file($path)) {
    print htmlspecialchars(file_get_contents($path), ENT_QUOTES, 'UTF-8');
}
?></textarea>
</div>
<pre id="printable"></pre>
<script>
function uploadContent() {
    if (content !== textarea.value) {
        var temp = textarea.value;
        var request = new XMLHttpRequest();
        request.open('POST', window.location.href, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onload = function() {
            if (request.readyState === 4) {
                content = temp;
                setTimeout(uploadContent, 1000);
            }
        }
        request.onerror = function() {
            setTimeout(uploadContent, 1000);
        }
        request.send('text=' + encodeURIComponent(temp));
        printable.removeChild(printable.firstChild);
        printable.appendChild(document.createTextNode(temp));
    }
    else {
        setTimeout(uploadContent, 1000);
    }
}
var textarea = document.getElementById('content');
var printable = document.getElementById('printable');
var content = textarea.value;
printable.appendChild(document.createTextNode(content));
textarea.focus();
uploadContent();
</script>
</body>
</html>
