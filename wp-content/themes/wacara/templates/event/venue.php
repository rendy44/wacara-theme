<?php
/**
 * Custom template to render venue section in event landing.
 *
 * @author  Rendy
 * @package Wacara
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<section class="bg-light venue" id="venue" data-aos="zoom-in">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-3">
                <h2 class="section-heading" data-aos="fade-left" data-aos-delay="200">The Venue</h2>
                <p class="lead" data-aos="fade-right" data-aos-delay="400">We would like to introduce the special place
                    where the event is going to be held</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-map">
                <div class="embed-responsive" data-aos="zoom-in" data-aos-delay="600">
                    <iframe class="embed-responsive-item"
                            src="https://maps.google.com/maps?q=Masjid%20pogung%20dalangan&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed"></iframe>
                </div>
            </div>
            <div class="col-lg-6 d-flex flex-column">
                <h3 data-aos="fade-left" data-aos-delay="800">Masjid Pogung Dalangan</h3>
                <p data-aos="fade-left" data-aos-delay="1000">Pogung Dalangan SIA XVI, RT.11/RW.50, Pogung Kidul,
                    Sinduadi, Kec. Mlati, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55284</p>
                <div class="carousel slide" id="gallery" data-ride="carousel" data-aos="fade-left"
                     data-aos-delay="1200">
                    <!-- Indicators-->
                    <ul class="carousel-indicators">
                        <li class="active" data-target="#gallery" data-slide-to="0"></li>
                        <li data-target="#gallery" data-slide-to="1"></li>
                    </ul>
                    <!-- The slideshow-->
                    <div class="carousel-inner">
                        <div class="carousel-item active"><img src="img/image1.jpg" alt="Los Angeles"></div>
                        <div class="carousel-item"><img src="img/image2.jpg" alt="Chicago"></div>
                    </div>
                    <!-- Left and right controls-->
                    <a class="carousel-control-prev" href="#gallery" data-slide="prev"><span
                                class="carousel-control-prev-icon"></span></a>
                    <a class="carousel-control-next" href="#gallery" data-slide="next"><span
                                class="carousel-control-next-icon"></span></a>
                </div>
            </div>
        </div>
    </div>
</section>

