<?php

namespace Puleeno\NhanhVn\Entities\Product;

use Puleeno\NhanhVn\Entities\AbstractEntity;

/**
 * Product Website Info entity
 */
class ProductWebsiteInfo extends AbstractEntity
{
    protected function validate(): void
    {
        // Basic validation - website info có thể empty
        // Không cần validation nghiêm ngặt
    }

    // Basic getters
    public function getMetaTitle(): ?string
    {
        return $this->getAttribute('metaTitle');
    }

    public function getMetaDescription(): ?string
    {
        return $this->getAttribute('metaDescription');
    }

    public function getMetaKeywords(): ?string
    {
        return $this->getAttribute('metaKeywords');
    }

    public function getHighlights(): array
    {
        return $this->getAttribute('highlights', []);
    }

    public function getTags(): array
    {
        return $this->getAttribute('tags', []);
    }

    public function getVideos(): array
    {
        return $this->getAttribute('videos', []);
    }

    // Business logic methods
    public function hasMetaTitle(): bool
    {
        return !empty($this->getMetaTitle());
    }

    public function hasMetaDescription(): bool
    {
        return !empty($this->getMetaDescription());
    }

    public function hasMetaKeywords(): bool
    {
        return !empty($this->getMetaKeywords());
    }

    public function hasHighlights(): bool
    {
        return !empty($this->getHighlights());
    }

    public function hasTags(): bool
    {
        return !empty($this->getTags());
    }

    public function hasVideos(): bool
    {
        return !empty($this->getVideos());
    }

    public function hasAnyMetaData(): bool
    {
        return $this->hasMetaTitle() || $this->hasMetaDescription() || $this->hasMetaKeywords();
    }

    public function hasAnyContent(): bool
    {
        return $this->hasHighlights() || $this->hasTags() || $this->hasVideos();
    }

    public function hasAnyWebsiteInfo(): bool
    {
        return $this->hasAnyMetaData() || $this->hasAnyContent();
    }

    public function getHighlightsCount(): int
    {
        return count($this->getHighlights());
    }

    public function getTagsCount(): int
    {
        return count($this->getTags());
    }

    public function getVideosCount(): int
    {
        return count($this->getVideos());
    }

    public function getHighlightsText(): string
    {
        $highlights = $this->getHighlights();
        return implode(', ', $highlights);
    }

    public function getTagsText(): string
    {
        $tags = $this->getTags();
        return implode(', ', $tags);
    }

    public function getVideosText(): string
    {
        $videos = $this->getVideos();
        return implode(', ', $videos);
    }

    public function getShortMetaTitle(int $length = 60): string
    {
        $title = $this->getMetaTitle() ?: '';
        if (strlen($title) <= $length) {
            return $title;
        }

        return substr($title, 0, $length) . '...';
    }

    public function getShortMetaDescription(int $length = 160): string
    {
        $description = $this->getMetaDescription() ?: '';
        if (strlen($description) <= $length) {
            return $description;
        }

        return substr($description, 0, $length) . '...';
    }

    public function getShortMetaKeywords(int $length = 200): string
    {
        $keywords = $this->getMetaKeywords() ?: '';
        if (strlen($keywords) <= $length) {
            return $keywords;
        }

        return substr($keywords, 0, $length) . '...';
    }

    public function getFormattedHighlights(): string
    {
        $highlights = $this->getHighlights();
        if (empty($highlights)) {
            return 'N/A';
        }

        $formatted = [];
        foreach ($highlights as $index => $highlight) {
            $formatted[] = ($index + 1) . '. ' . $highlight;
        }

        return implode("\n", $formatted);
    }

    public function getFormattedTags(): string
    {
        $tags = $this->getTags();
        if (empty($tags)) {
            return 'N/A';
        }

        return '#' . implode(' #', $tags);
    }

    public function getFormattedVideos(): string
    {
        $videos = $this->getVideos();
        if (empty($videos)) {
            return 'N/A';
        }

        $formatted = [];
        foreach ($videos as $index => $video) {
            $formatted[] = ($index + 1) . '. ' . $video;
        }

        return implode("\n", $formatted);
    }

    public function getMetaTitleLength(): int
    {
        return strlen($this->getMetaTitle() ?: '');
    }

    public function getMetaDescriptionLength(): int
    {
        return strlen($this->getMetaDescription() ?: '');
    }

    public function getMetaKeywordsLength(): int
    {
        return strlen($this->getMetaKeywords() ?: '');
    }

    public function isMetaTitleOptimal(): bool
    {
        $length = $this->getMetaTitleLength();
        return $length >= 30 && $length <= 60;
    }

    public function isMetaDescriptionOptimal(): bool
    {
        $length = $this->getMetaDescriptionLength();
        return $length >= 120 && $length <= 160;
    }

    public function isMetaKeywordsOptimal(): bool
    {
        $length = $this->getMetaKeywordsLength();
        return $length <= 200;
    }

    public function getMetaTitleStatus(): string
    {
        if (!$this->hasMetaTitle()) {
            return 'Missing';
        }

        if ($this->isMetaTitleOptimal()) {
            return 'Optimal';
        }

        $length = $this->getMetaTitleLength();
        if ($length < 30) {
            return 'Too Short';
        }

        return 'Too Long';
    }

    public function getMetaDescriptionStatus(): string
    {
        if (!$this->hasMetaDescription()) {
            return 'Missing';
        }

        if ($this->isMetaDescriptionOptimal()) {
            return 'Optimal';
        }

        $length = $this->getMetaDescriptionLength();
        if ($length < 120) {
            return 'Too Short';
        }

        return 'Too Long';
    }

    public function getMetaKeywordsStatus(): string
    {
        if (!$this->hasMetaKeywords()) {
            return 'Missing';
        }

        if ($this->isMetaKeywordsOptimal()) {
            return 'Optimal';
        }

        return 'Too Long';
    }

    public function getMetaTitleColor(): string
    {
        $status = $this->getMetaTitleStatus();

        switch ($status) {
            case 'Missing':
                return '#dc3545'; // Red
            case 'Optimal':
                return '#28a745'; // Green
            case 'Too Short':
                return '#ffc107'; // Yellow
            case 'Too Long':
                return '#fd7e14'; // Orange
            default:
                return '#6c757d'; // Gray
        }
    }

    public function getMetaDescriptionColor(): string
    {
        $status = $this->getMetaDescriptionStatus();

        switch ($status) {
            case 'Missing':
                return '#dc3545'; // Red
            case 'Optimal':
                return '#28a745'; // Green
            case 'Too Short':
                return '#ffc107'; // Yellow
            case 'Too Long':
                return '#fd7e14'; // Orange
            default:
                return '#6c757d'; // Gray
        }
    }

    public function getMetaKeywordsColor(): string
    {
        $status = $this->getMetaKeywordsStatus();

        switch ($status) {
            case 'Missing':
                return '#dc3545'; // Red
            case 'Optimal':
                return '#28a745'; // Green
            case 'Too Long':
                return '#fd7e14'; // Orange
            default:
                return '#6c757d'; // Gray
        }
    }

    public function getWebsiteInfoSummary(): string
    {
        $summary = [];

        if ($this->hasMetaTitle()) {
            $summary[] = "Title: {$this->getShortMetaTitle()}";
        }

        if ($this->hasMetaDescription()) {
            $summary[] = "Description: {$this->getShortMetaDescription()}";
        }

        if ($this->hasHighlights()) {
            $summary[] = "Highlights: {$this->getHighlightsCount()} items";
        }

        if ($this->hasTags()) {
            $summary[] = "Tags: {$this->getTagsCount()} items";
        }

        if ($this->hasVideos()) {
            $summary[] = "Videos: {$this->getVideosCount()} items";
        }

        if (empty($summary)) {
            return 'No website info available';
        }

        return implode(' | ', $summary);
    }

    /**
     * Tạo website info từ array data
     */
    public static function createFromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * Tạo website info từ highlights array
     */
    public static function createFromHighlights(array $highlights): self
    {
        return new self(['highlights' => $highlights]);
    }

    /**
     * Tạo website info từ tags array
     */
    public static function createFromTags(array $tags): self
    {
        return new self(['tags' => $tags]);
    }

    /**
     * Tạo website info từ videos array
     */
    public static function createFromVideos(array $videos): self
    {
        return new self(['videos' => $videos]);
    }

    /**
     * Tạo website info từ meta data
     */
    public static function createFromMetaData(string $title = '', string $description = '', string $keywords = ''): self
    {
        $data = [];

        if (!empty($title)) {
            $data['metaTitle'] = $title;
        }

        if (!empty($description)) {
            $data['metaDescription'] = $description;
        }

        if (!empty($keywords)) {
            $data['metaKeywords'] = $keywords;
        }

        return new self($data);
    }

    /**
     * Merge nhiều website info
     */
    public static function merge(array $websiteInfos): self
    {
        $merged = [];

        foreach ($websiteInfos as $websiteInfo) {
            if ($websiteInfo instanceof self) {
                $data = $websiteInfo->toArray();
                $merged = array_merge_recursive($merged, $data);
            }
        }

        return new self($merged);
    }

    /**
     * Validate meta title length
     */
    public static function validateMetaTitleLength(string $title): bool
    {
        $length = strlen($title);
        return $length >= 30 && $length <= 60;
    }

    /**
     * Validate meta description length
     */
    public static function validateMetaDescriptionLength(string $description): bool
    {
        $length = strlen($description);
        return $length >= 120 && $length <= 160;
    }

    /**
     * Validate meta keywords length
     */
    public static function validateMetaKeywordsLength(string $keywords): bool
    {
        $length = strlen($keywords);
        return $length <= 200;
    }

    /**
     * Generate meta title từ product name
     */
    public static function generateMetaTitle(string $productName, string $brand = '', string $category = ''): string
    {
        $title = $productName;

        if (!empty($brand)) {
            $title .= " - {$brand}";
        }

        if (!empty($category)) {
            $title .= " | {$category}";
        }

        // Giới hạn độ dài
        if (strlen($title) > 60) {
            $title = substr($title, 0, 57) . '...';
        }

        return $title;
    }

    /**
     * Generate meta description từ product description
     */
    public static function generateMetaDescription(string $description, int $maxLength = 160): string
    {
        if (strlen($description) <= $maxLength) {
            return $description;
        }

        // Cắt theo từ, không cắt giữa từ
        $truncated = substr($description, 0, $maxLength);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }
}
