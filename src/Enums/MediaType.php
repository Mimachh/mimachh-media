<?php

namespace Mimachh\Media\Enums;

enum MediaType: string {
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case DOCUMENT = 'document';
    case OTHER = 'other';
}