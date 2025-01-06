<?php
echo view("template/header_default", $dados, $param);
echo view("{$view}", $dados, $param);
echo view("template/footer_default", $dados, $param);