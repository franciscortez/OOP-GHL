<?php

class TokenStorage
{
  private $filePath;

  public function __construct($filePath = __DIR__ . '/../storage/tokens.json')
  {
    $this->filePath = $filePath;

    if (!file_exists(dirname($this->filePath))) {
      mkdir(dirname($this->filePath), 0777, true);
    }
  }

  public function save(array $tokens)
  {
    return file_put_contents($this->filePath, json_encode($tokens, JSON_PRETTY_PRINT));
  }

  public function get()
  {
    if (!file_exists($this->filePath)) {
      return null;
    }

    return json_decode(file_get_contents($this->filePath), true);
  }

  public function hasToken()
  {
    return file_exists($this->filePath);
  }
}
