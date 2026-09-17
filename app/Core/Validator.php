<?php

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $message): self
    {
        $value = $this->data[$field] ?? '';
        if (!is_array($value) && trim((string) $value) === '') {
            $this->addError($field, $message);
        }
        if (is_array($value) && $value === []) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $message): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value !== '' && mb_strlen($value) < $min) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $message): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if (mb_strlen($value) > $max) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function phone(string $field, string $message): self
    {
        $value = preg_replace('/\D+/', '', (string) ($this->data[$field] ?? ''));
        if ($value !== '' && strlen($value) < 10) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function email(string $field, string $message, bool $optional = true): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));
        if ($value === '' && $optional) {
            return $this;
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function numeric(string $field, string $message): self
    {
        $value = $this->data[$field] ?? '';
        if (!is_numeric(str_replace(',', '.', (string) $value))) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function min(string $field, float $min, string $message): self
    {
        $value = (float) str_replace(',', '.', (string) ($this->data[$field] ?? 0));
        if ($value < $min) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function in(string $field, array $allowed, string $message): self
    {
        if (!in_array($this->data[$field] ?? null, $allowed, true)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors === [] ? null : reset($this->errors);
    }
}
