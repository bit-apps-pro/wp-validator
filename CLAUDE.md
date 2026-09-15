# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

`bitapps/wp-validator` — a Composer library (PSR-4: `BitApps\WPValidator\` → [src/](src/)) providing Laravel-style validation and sanitization for WordPress. Public API and the full rule list are documented in [README.md](README.md); update it when adding a rule or sanitizer.

**PHP 7.2 is the floor.** No typed properties, no arrow functions, no null coalescing assignment, no union types. Return types on methods are used and fine. [rector.php](rector.php) pins `PhpVersion::PHP_72` and skips the typed-property rectors for this reason.

## Commands

```bash
composer test:unit                                  # full suite (Pest, --testdox, excludes group 'db')
./vendor/bin/pest tests/Rules/BetweenTest.php       # single file
./vendor/bin/pest --filter='between accepts a length of zero'   # single test by name
composer compat                                     # PHPCompatibility check against PHP 7.2
composer rector                                     # Rector dry-run (never auto-applies)
```

There is no `phpunit.xml` — Pest runs on defaults with paths passed explicitly. `.gitattributes` references one that does not exist; harmless.

## Architecture

The validation pass flows through four collaborators:

- [Validator.php](src/Validator.php) — orchestrator. `make()` is the only entry point; it returns `$this` so `fails()` / `errors()` / `validated()` chain off it.
- [InputDataContainer.php](src/InputDataContainer.php) — holds the input array plus the *currently-focused* field key and label. Rules read the value under validation from here rather than receiving the whole dataset. It resolves dot-paths on both get and set, so nested writes from sanitizers land in the right place.
- [Rule.php](src/Rule.php) — abstract base. Subclasses implement `validate($value)` and `message()`.
- [ErrorBag.php](src/ErrorBag.php) — builds the message, resolving custom messages and substituting `:placeholder` tokens.

### Rule resolution is convention-based

`resolveRule()` converts a string rule name to a class: `digit_between` → `BitApps\WPValidator\Rules\DigitBetweenRule`. Snake_case becomes StudlyCase and `Rule` is appended. There is no registry — **the class name is the contract**, and [tests/ArchitectureTest.php](tests/ArchitectureTest.php) enforces it (every class in `Rules\` must extend `Rule`, end in `Rule`, and define `validate` + `message`).

That same arch test bans `die`, `exit`, `error_log`, `var_dump`, and `print_r` anywhere in the codebase.

### Parameters

`min:8` and `between:1,5` are parsed by splitting on the first `:`, then on `,`. Positional values are zipped against the rule's `getParamKeys()` into a name→value map. So a parameterized rule needs three things: a `$requireParameters` array, a `getParamKeys()` returning it, and a `checkRequiredParameter()` call at the top of `validate()`. Those same parameter names double as error-message placeholders (`:min`, `:max`, `:size`, `:other`) — `ErrorBag` merges the parameter map straight into the placeholder set, so naming a parameter automatically makes the token available.

Note `setParameterValues()` silently no-ops when the key and value counts differ; the resulting missing parameter is what `checkRequiredParameter()` catches, throwing `Exception\InvalidArgumentException`.

### Sanitization runs before validation

In `validateByRules()`, any rule string containing `sanitize` is stripped out and applied *first*, mutating the value in the container; the remaining rules then validate the sanitized value. `sanitize:html_class` maps to `sanitizeHtmlClass()` in [SanitizationMethods.php](src/SanitizationMethods.php) (prefix + StudlyCase suffix), with `sanitize:wp_kses|a.href,br` passing its pipe-delimited allowlist as a second argument.

Validation of a field **stops at the first failing rule** (`break`), so each field yields at most one error message per pass.

### Special-cased rules

Three rules are not purely self-contained and need care when touching `Validator::validateField()`:

- `nullable` — intercepted *before* any rule runs. If present and the value is empty, the field short-circuits as valid. `NullableRule::validate()` itself just returns `true`.
- `present` — checks key existence rather than value, and short-circuits the remaining rules when the value is empty.
- `same:other` — reads a sibling field out of the container, not just `$value`.

### Wildcards

`by_role.*.path` expands via `processWildcardFieldKey()` into one concrete dot-path per matching entry before validation. Errors are keyed by the *expanded* path, while the custom-label lookup uses the original wildcard string. Note the expansion walks only the **first** key at each `*` level to discover structure — see [tests/ValidateWildcardPathTest.php](tests/ValidateWildcardPathTest.php) for the shape it handles.

### Empty vs. length

Two helpers in [Helpers.php](src/Helpers.php) carry subtle semantics that rules must not reimplement:

- `isEmpty()` treats `'0'`, `0`, `0.0`, and `false` as **non**-empty.
- `getValueLength()` returns `mb_strlen` for strings, `count` for arrays, the number itself for ints/floats, and `false` for anything unmeasurable (bool, null, object). `false` is distinct from length `0` — a length of 0 is valid.

`lengthWithin($value, $min, $max)` wraps both and is what `between`, `min`, `max`, and `size` should use (`null` for an open bound) rather than comparing lengths by hand.

## Testing

Pest, with `uses()->compact()` in [tests/Pest.php](tests/Pest.php). Rule tests overwhelmingly instantiate the rule directly and call `validate()` — no `Validator` needed unless the behavior involves the container, wildcards, or rule interaction:

```php
$rule = new BetweenRule();
$rule->setParameterValues(['min', 'max'], [1, 5]);
expect(true)->toBe($rule->validate('abcde'));
```

**WordPress is not loaded and there are no WP function stubs.** Everything in `SanitizationMethods` calls WP core (`sanitize_text_field`, `wp_kses`, `esc_url_raw`, …) and will fatal outside WordPress, which is why no test covers sanitization. Adding sanitizer tests means introducing stubs (e.g. Brain Monkey) first — don't assume the harness is there.

The `db` group is excluded from `test:unit`; nothing currently uses it.
