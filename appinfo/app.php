<?php

\OCP\App::addNavigationEntry(array(

    'id' => 'jot',
    'order' => 100,
    'href' => \OCP\Util::linkToRoute('jot.page.index'),
    'icon' => \OCP\Util::imagePath('jot', 'icon.svg'),
    'name' => 'Jot',//\OC_L10N::get('Jot')->t('Jot'),
    ));
