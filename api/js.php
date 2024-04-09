<?php
/**
 * https://github.com/juicyfx/vercel-examples/commit/1fcbe3ff98ae34830cfd779224433cca16bb4f93
 * // "src": "/(css|js)/(.*)$", 
 * // "dest": "/api/assets.php?file=$1&type=$2"
 */
header("Content-type: text/js; charset: UTF-8");
echo require __DIR__ . '/../public/build/assets/' . basename($_GET['file']);