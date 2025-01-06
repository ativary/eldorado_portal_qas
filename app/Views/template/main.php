<?php
echo view("template/header", $dados, $param);
echo view("{$view}", $dados, $param);
echo view("template/footer", $dados, $param);
unset($dados, $param);