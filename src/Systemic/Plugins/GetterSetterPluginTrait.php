<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Plugins;

use DecodeLabs\Systemic\Context;

use DecodeLabs\Glitch;
use DecodeLabs\Glitch\Inspectable;
use DecodeLabs\Glitch\Dumper\Entity;
use DecodeLabs\Glitch\Dumper\Inspector;

trait GetterSetterPluginTrait
{
    protected $fetcher;
    protected $fetched = false;
    protected $listeners = [];
    protected $context;

    /**
     * Init with parent factory
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Set output locale
     */
    public function set($value): self
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

    abstract protected function setCurrent($value);

    /**
     * Get output locale
     */
    public function get()
    {
        $output = $this->getCurrent();

        if (!$this->fetched && $this->fetcher) {
            $fetcher = $this->fetcher;
            $new = $fetcher();
            $this->fetched = true;

            if ($new !== null && !$this->compare($new, $output)) {
                $this->set($new);
                $output = $new;
            }
        }

        return $output;
    }

    abstract protected function getCurrent();

    /**
     * Compare old and new
     */
    protected function compare($a, $b): bool
    {
        return $a === $b;
    }

    /**
     * Set locale fetcher
     */
    public function setFetcher(?callable $fetcher): self
    {
        $this->fetcher = $fetcher;
        return $this;
    }

    /**
     * Register locale listener
     */
    public function registerListener(string $name, callable $listener): self
    {
        $this->listeners[$name] = $listener;
        return $this;
    }

    /**
     * Unregister locale listener
     */
    public function unregisterListener(string $name): self
    {
        unset($this->listeners[$name]);
        return $this;
    }



    /**
     * Inspect for Glitch
     */
    public function glitchInspect(Entity $entity, Inspector $inspector): void
    {
        $entity
            ->setSingleValue($inspector($this->get()))
            ->setSectionVisible('properties', false);

        $inspector->inspectClassMembers($this, new \ReflectionClass($this), $entity, ['context']);
    }
}
