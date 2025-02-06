<?php

namespace App\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CkeditorType extends AbstractType
{
    public const ALLOWED_TAGS = [
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'p',
        'strong',
        'ol',
        'li',
        'ul',
        'i',
        's',
        'u',
        'a',
        'table',
        'thead',
        'tbody',
        'tr',
        'td',
        'th',
        'figure',
        'mark',
        'blockquote',
        'label',
        'span',
        'pre',
        'code'
    ];

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['pattern'] = null;
        unset($view->vars['attr']['pattern']);

        /* change ID to editor so it will be replaces with ckeditor content */
        unset($view->vars['id']);
        $view->vars['id'] = 'editor';
        $view->vars['attr']['name'] = 'editor';
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }
}
