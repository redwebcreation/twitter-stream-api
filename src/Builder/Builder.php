<?php


namespace RWC\TwitterStream\Builder;


use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionProperty;

class Builder
{
    protected ?int $sample = null;
    protected string $query;
    protected array $is = [];
    protected array $has = [];
    protected array $lang = [];
    protected array $from = [];
    protected array $to = [];
    protected array $context = [];
    protected array $retweetsOf = [];
    protected array $url = [];
    protected array $entity = [];
    protected array $conversationId = [];
    protected array $bio = [];
    protected array $bioName = [];
    protected array $bioLocation = [];
    protected array $place = [];
    protected array $placeCountry = [];
    protected array $pointRadius = [];
    protected array $boundingBox = [];

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    #[Pure] public static function create(string $query): Builder
    {
        return new self($query);
    }

    public function __toString(): string
    {
        $rule = [$this->query];

        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            if ($property->getName() === 'query') {
                continue;
            }

            $propertyName = strtolower(preg_replace_callback(
                "/(?'lowercase'[a-z])(?'uppercase'[A-Z])/",
                fn($matches) => $matches['lowercase'] . '_' . strtolower($matches['uppercase']),
                $property->getName()
            ));

            $property->setAccessible(true);
            $propertyValue = $property->getValue($this);

            if (empty($propertyValue)) {
                continue;
            }

            if (is_array($propertyValue)) {
                foreach ($propertyValue as $condition) {
                    if (is_array($condition)) {
                        $condition = '[' . implode(' ', $condition) . ']';
                    }
                    $rule[] = $propertyName . ':' . $condition;
                }
            } else {
                $rule[] = $propertyName . ':' . $propertyValue;
            }
        }

        return implode(' ', $rule);
    }

    public function from(string|array $users): static
    {
        $this->from = is_array($users) ? $users : [$users];
        return $this;
    }

    public function to(string|array $users): static
    {
        $this->to = is_array($users) ? $users : [$users];
        return $this;
    }

    public function sampleSize(int $size): static
    {
        if (0 >= $size || $size > 100) {
            throw new InvalidArgumentException('The sample size must be between 0 and 100 percents');
        }

        $this->sample = $size;
        return $this;
    }

    public function replies(): static
    {
        $this->is[] = 'reply';
        return $this;
    }

    public function retweets(): static
    {
        $this->is[] = 'retweet';
        return $this;
    }

    public function quote(): static
    {
        $this->is[] = 'quote';
        return $this;
    }

    public function verified(): static
    {
        $this->is[] = 'verified';
        return $this;
    }

    public function retweetsOf(string|array $users): static
    {
        $this->retweetsOf = is_array($users) ? $users : [$users];
        return $this;
    }

    public function context(string|array $context): static
    {
        $this->context[] = is_array($context) ? $context : [$context];
        return $this;
    }

    public function hashtags(): static
    {
        $this->has[] = 'hashtags';
        return $this;
    }

    public function hasCashtags(): static
    {
        $this->has[] = 'cashtags';
        return $this;
    }

    public function hasLinks(): static
    {
        $this->has[] = 'links';
        return $this;
    }

    public function hasMentions(): static
    {
        $this->has[] = 'mentions';
        return $this;
    }

    public function hasMedia(): static
    {
        $this->has[] = 'media';
        return $this;
    }

    public function hasImages(): static
    {
        $this->has[] = 'images';
        return $this;
    }

    public function hasVideos(): static
    {
        $this->has[] = 'videos';
        return $this;
    }

    public function hasGeographicDataAttached(): static
    {
        $this->has[] = 'geo';
        return $this;
    }

    public function locale(string $lang): static
    {
        $this->lang[] = $lang;
        return $this;
    }

    public function url(string|array $urls): static
    {
        $this->url = is_array($urls) ? $urls : [$urls];
        return $this;
    }

    public function entity(string|array $entities): static
    {
        $this->entity = is_array($entities) ? $entities : [$entities];
        return $this;
    }

    public function conversation(string|array $conversations): static
    {
        $this->conversationId = is_array($conversations) ? $conversations : [$conversations];
        return $this;
    }

    public function bio(string|array $bios): static
    {
        $this->bio = is_array($bios) ? $bios : [$bios];
        return $this;
    }

    public function bioName(string|array $bioNames): static
    {
        $this->bioName = is_array($bioNames) ? $bioNames : [$bioNames];
        return $this;
    }

    public function bioLocation(string|array $bioLocations): static
    {
        $this->bioLocation = is_array($bioLocations) ? $bioLocations : [$bioLocations];
        return $this;
    }

    public function place(string|array $places): static
    {
        $this->place = is_array($places) ? $places : [$places];
        return $this;
    }

    public function placeCountry(string|array $placesCountry): static
    {
        $this->placeCountry = is_array($placesCountry) ? $placesCountry : [$placesCountry];
        return $this;
    }

    public function pointRadius(array $points): static
    {
        $isCollection = array_reduce($points, function ($_, $point) {
            return $_ || is_array($point);
        }, false);

        $this->pointRadius = $isCollection ? $points : [$points];
        return $this;
    }

    public function boundingBox(array $boxes): static
    {

        $isCollection = array_reduce($boxes, function ($_, $box) {
            return $_ || is_array($box);
        }, false);

        $this->boundingBox = $isCollection ? $boxes : [$boxes];
        return $this;
    }
}