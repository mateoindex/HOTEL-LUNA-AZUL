<?php

namespace App\Core;

/**
 * validador minimo, no apunta a ser exhaustivo
 * reglas separadas por |  ej: 'required|email|max:120'
 */
class Validator {

    private array $errors = [];
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public static function make(array $data): self {
        return new self($data);
    }

    public function check(string $field, string $rules, ?string $label = null): self {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? null;

        foreach (explode('|', $rules) as $rule) {
            [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);

            switch ($name) {
                case 'required':
                    if ($value === null || $value === '') {
                        $this->errors[$field][] = "{$label} es obligatorio.";
                    }
                    break;
                case 'email':
                    if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field][] = "{$label} no es un correo válido.";
                    }
                    break;
                case 'min':
                    if (is_string($value) && strlen($value) < (int) $param) {
                        $this->errors[$field][] = "{$label} debe tener al menos {$param} caracteres.";
                    }
                    break;
                case 'max':
                    if (is_string($value) && strlen($value) > (int) $param) {
                        $this->errors[$field][] = "{$label} no puede exceder {$param} caracteres.";
                    }
                    break;
                case 'int':
                    if ($value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                        $this->errors[$field][] = "{$label} debe ser un número entero.";
                    }
                    break;
                case 'numeric':
                    if ($value !== null && $value !== '' && !is_numeric($value)) {
                        $this->errors[$field][] = "{$label} debe ser numérico.";
                    }
                    break;
                case 'date':
                    if ($value && !strtotime($value)) {
                        $this->errors[$field][] = "{$label} debe ser una fecha válida.";
                    }
                    break;
                case 'in':
                    $opts = explode(',', (string) $param);
                    if ($value !== null && $value !== '' && !in_array((string) $value, $opts, true)) {
                        $this->errors[$field][] = "{$label} no tiene un valor válido.";
                    }
                    break;
            }
        }
        return $this;
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstErrors(): array {
        $out = [];
        foreach ($this->errors as $f => $errs) {
            $out[$f] = $errs[0];
        }
        return $out;
    }
}
