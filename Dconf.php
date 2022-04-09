<?php
declare(strict_types=1);
namespace Vas;

class Dconf {
    static function read(string $x): ?string {
        return shell_exec("dconf read {$x}");
    }

    static function dump(string $x): ?string {
        return shell_exec("dconf dump {$x}");
    }

    static function list(string $x): ?array {
        $data = shell_exec("dconf read {$x}");
        return $data
            ? explode("\n", $data)
            : null;
    }

    static function write(string $key, $value): void {
        exec("dconf write {$key} \"{$Dconf::serialise($value)}\"");
    }


    private static function serialise($x): string {
        switch (gettype($x)) {
            case 'integer':
            case 'double':
                return strval($x);
            case 'string':
                return Dconf::serialise_string($x);
            case 'boolean':
                return $x ? 'true' : 'false';
            case 'array':
                return Dconf::serialise_array($x);
            default:
                throw new \Exception('unsupported type: ' . gettype($x));
        }
    }

    private static function serialise_string(string $x): string {
        $xs = [];
        foreach ($x as $c) array_push(Dconf::escape_char($c));
        return '.' . implode('', $xs) . '.';
    }

    private static function escape_char(string $x): string {
        switch ($x) {
            case "\\": return "\\\\";
            case "\t": return "\\t";
            case "\n": return "\\n";
            case "$": return "\\$";
            case "\"": return "\\\"";
            default: return $x;
        }
    }

    private static function serialise_array(array $xs): string {
        $r = [];
        foreach ($xs as $x) array_push($rs, Dconf::serialise($x));
        return '[' . implode(',', $r) . ']';
    }
}
