<?php

namespace app\Models\Entities;

class Character
{
    private int $id;
    private string $name;
    private string $image;
    private string $data;
    private int $maxHealth;
    private int $curHealth;
    private array $charModifiers;
    private int $initiative;
    private string $role;
    private string $type;
    private ?string $owner;

    /**
     * @param int $id
     * @param string $name
     * @param string $image
     * @param string $data
     * @param int $maxHealth
     * @param int $curHealth
     * @param string $charModifiers
     * @param int $initiative
     * @param string $role
     * @param string $type
     * @param string|null $owner
     */
    public function __construct(
            int $id,
            string $name,
            string $image,
            string $data,
            int $maxHealth,
            int $curHealth,
            string $charModifiers,
            int $initiative,
            string $role,
            string $type,
            ?string $owner
        )
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->data = $data;
        $this->maxHealth = $maxHealth;
        $this->curHealth = $curHealth;
        $this->charModifiers = ($charModifiers === '') ? [] : json_decode($charModifiers,true);
        $this->initiative = $initiative;
        $this->role = $role;
        $this->type = $type;
        $this->owner = $owner;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getMaxHealth(): int
    {
        return $this->maxHealth;
    }

    public function getCurHealth(): int
    {
        return $this->curHealth;
    }

    public function getCharModifiers(): array
    {
        return $this->charModifiers;
    }

    public function getInitiative(): int
    {
        return $this->initiative;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function setHP(int $current, int $max)
    {
        $this->maxHealth = $max;
        $this->curHealth = $current;
        return $this;
    }

    public function setCharModifiers(array $content)
    {
        $this->charModifiers = $content;
        return $this;
    }

    public function setInitiative(int $initiative)
    {
        $this->initiative = $initiative;
        return $this;
    }

    public function setData(string $data)
    {
        $this->data = $data;
        return $this;
    }


}