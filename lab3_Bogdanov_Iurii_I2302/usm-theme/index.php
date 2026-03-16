<?php get_header(); ?>

<section class="posts-section">
<h2 class="section-title">Последние записи</h2>

<div class="posts-container">

<?php
$count = 0;

if (have_posts()) :
while (have_posts()) : the_post();

if ($count == 5) break;
?>

<div class="post-card">
<h3><?php the_title(); ?></h3>
<p><?php the_excerpt(); ?></p>
</div>

<?php
$count++;
endwhile;
endif;
?>

</div>
</section>


<section class="features-section">

<h2 class="section-title">Возможности темы</h2>

<div class="features-grid">

<div class="feature-card">
<h3>Простая структура</h3>
<p>Тема содержит обязательные файлы WordPress и базовые шаблоны страниц.</p>
</div>

<div class="feature-card">
<h3>Шаблоны страниц</h3>
<p>Используются отдельные шаблоны для записей, страниц и архивов.</p>
</div>

<div class="feature-card">
<h3>Подключение стилей</h3>
<p>Стили подключаются через functions.php с помощью wp_enqueue_style.</p>
</div>

<div class="feature-card">
<h3>WordPress Loop</h3>
<p>Последние записи выводятся с использованием стандартного WordPress Loop.</p>
</div>

<div class="feature-card">
<h3>Гибкая структура</h3>
<p>Шапка, подвал и сайдбар подключаются через отдельные шаблоны.</p>
</div>

<div class="feature-card">
<h3>Расширяемость</h3>
<p>Тема легко расширяется дополнительными шаблонами и стилями.</p>
</div>

</div>

</section>

<section class="services-section">

<h2 class="section-title">Наши услуги</h2>

<div class="services-grid">

<div class="service-card">
<h3>Комфортные номера</h3>
<p>Гостям предлагаются современные и уютные номера с высоким уровнем комфорта.</p>
</div>

<div class="service-card">
<h3>Бесплатный Wi-Fi</h3>
<p>На всей территории отеля доступен бесплатный высокоскоростной интернет.</p>
</div>

<div class="service-card">
<h3>Ресторан</h3>
<p>В ресторане отеля гости могут попробовать блюда европейской и местной кухни.</p>
</div>

<div class="service-card">
<h3>Трансфер</h3>
<p>Для гостей доступен трансфер из аэропорта и обратно по предварительному запросу.</p>
</div>

<div class="service-card">
<h3>Парковка</h3>
<p>На территории отеля предусмотрена удобная и безопасная парковка для гостей.</p>
</div>

<div class="service-card">
<h3>Круглосуточная стойка</h3>
<p>Персонал отеля работает круглосуточно и всегда готов помочь гостям.</p>
</div>

</div>

</section>

<?php get_footer(); ?>