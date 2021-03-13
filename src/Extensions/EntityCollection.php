<?php

namespace Kusikusi\Extensions;

class EntityCollection extends \Illuminate\Database\Eloquent\Collection
{
    public function mediaWithTag($tag) {
        return $this->whereIncludes('media_tags', $tag);
    }
    public function mediumWithTag($tag) {
        return $this->mediaWithTag($tag)->first();
    }
    public function mediaOfFormat($format) {
        return $this->whereProperty('format', $format);
    }
    public function mediumOfFormat($format) {
        return $this->mediaOfFormat($format)->first();
    }
    public function whereIncludes($key, $value, $strict = false) {
        return $this->filter(function ($item) use ($key, $value, $strict) {
            return in_array($value, data_get($item, $key) ?? [], $strict);
        });
    }
    public function firstWhereIncludes($key, $value, $strict = false) {
        return $this->whereIncludes($key, $value, $strict)->first();
    }
    public function mediaIsImage() {
        return $this->whereProperty('isImage', true);
    }
    public function mediumIsImage() {
        return $this->mediaIsImage()->first();
    }
    public function mediaIsAudio() {
        return $this->whereProperty('isAudio', true);
    }
    public function mediumIsAudio() {
        return $this->mediaIsAudio()->first();
    }
    public function mediaIsVideo() {
        return $this->whereProperty('isVideo', true);
    }
    public function mediumIsVideo() {
        return $this->mediaIsVideo()->first();
    }
    public function mediaIsWebImage() {
        return $this->whereProperty('isWebImage', true);
    }
    public function mediumIsWebImage() {
        return $this->mediaIsWebImage()->first();
    }
    public function mediaIsWebAudio() {
        return $this->whereProperty('isWebAudio', true);
    }
    public function mediumIsWebAudio() {
        return $this->mediaIsWebAudio()->first();
    }
    public function mediaIsWebVideo() {
        return $this->whereProperty('isWebVideo', true);
    }
    public function mediumIsWebVideo() {
        return $this->mediaIsWebVideo()->first();
    }
    public function mediaIsDocument() {
        return $this->whereProperty('isDocument', true);
    }
    public function mediumIsDocument() {
        return $this->mediaIsDocument()->first();
    }
    public function whereProperty($key, $value) {
        return $this->filter(function ($item) use ($key, $value) {
            if (isset($item['properties']) &&  isset($item['properties'][$key])) {
                return $item['properties'][$key] === $value;
            }
            return false;
        });
    }
    public function firstWhereProperty($key, $value) {
        return $this->whereProperty($key, $value)->first();
    }
}
