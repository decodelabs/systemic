<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Plugins;

use DecodeLabs\Systemic\Context;

/**
 * @template T
 */
trait GetterSetterPluginTrait
{
    /**
     * @var callable|null
     */
    protected $fetcher;

    protected bool $fetched = false;

    /**
     * @var array<string, callable>
     */
    protected array $listeners = [];

    protected Context $context;

    /**
     * Init with parent factory
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Set output locale
     *
     * @phpstan-param T $value
     * @return $this
     */
    public function set(mixed $value): static
    {
        if (!empty($this->listeners)) {
            $prev = $this->getCurrent();
        } else {
            $prev = null;
        }

        $value = $this->setCurrent($value);

        if (!empty($this->listeners)) {
            foreach ($this->listeners as $listener) {
                $listener($value, $prev);
            }
        }

        return $this;
    }

    /**
     * @phpstan-param T $value
     */
    abstract protected function setCurrent(mixed $value);

    /**
     * Get output locale
     *
     * @phpstan-return T
     */
    public function get(): mixed
    {
        $output = $this->getCurrent();

        if (!$this->fetched && $this->fetcher) {
            $fetcher = $this->fetcher;
            $new = $fetcher();
            $this->fetched = true;

            if (
                $new !== null &&
                !$this->compare($new, $output)
            ) {
                $this->set($new);
                $output = $new;
            }
        }

        return $output;
    }

    /**
     * @phpstan-return T
     */
    abstract protected function getCurrent(): mixed;

    /**
     * Compare old and new
     *
     * @phpstan-param T $a
     * @phpstan-param T $b
     */
    protected function compare(
        mixed $a,
        mixed $b
    ): bool {
        return $a === $b;
    }

    /**
     * Set locale fetcher
     *
     * @return $this
     */
    public function setFetcher(?callable $fetcher): static
    {
        $this->fetcher = $fetcher;
        return $this;
    }

    /**
     * Register locale listener
     *
     * @return $this
     */
    public function registerListener(
        string $name,
        callable $listener
    ): static {
        $this->listeners[$name] = $listener;
        return $this;
    }

    /**
     * Unregister locale listener
     *
     * @return $this
     */
    public function unregisterListener(string $name): static
    {
        unset($this->listeners[$name]);
        return $this;
    }



    /**
     * Export for dump inspection
     */
    public function glitchDump(): iterable
    {
        yield 'value' => $this->get();
        yield 'section:properties' => false;
        yield 'classMembers' => ['context'];
    }
}
