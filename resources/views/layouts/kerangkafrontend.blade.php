<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Sbidu - Bid And Auction HTML Template</title>

    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/owl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/aos.css') }}">
    <link rel="stylesheet" href="{{ asset('sbidu/assets/css/main.css') }}">

    <link rel="shortcut icon" href="{{ asset('sbidu/assets/images/favicon.png') }}" type="image/x-icon">
</head>

<body>
    <!--============= ScrollToTop Section Starts Here =============-->
    <div class="overlayer" id="overlayer">
        <div class="loader">
            <div class="loader-inner"></div>
        </div>
    </div>
    <a href="#0" class="scrollToTop"><i class="fas fa-angle-up"></i></a>
    <div class="overlay"></div>
    <!--============= ScrollToTop Section Ends Here =============-->


    <!--============= Header Section Starts Here =============-->
    @include('layouts.frontend.header')
    <!--============= Header Section Ends Here =============-->
    @yield('content')

    <!--============= Footer Section Starts Here =============-->
    <footer class="bg_img padding-top oh" data-background="{{ asset('sbidu/assets/images/footer/footer-bg.jpg') }}" style="padding-top: 450px;">
        <div class="footer-top-shape">
            <img src="{{ asset('sbidu/assets/css/img/footer-top-shape.png') }}" alt="css">
        </div>
        <div class="anime-wrapper">
            <div class="anime-1 plus-anime">
                <img src="{{ asset('sbidu/assets/images/footer/p1.png') }}" alt="footer">
            </div>
            <div class="anime-2 plus-anime">
                <img src="{{ asset('sbidu/assets/images/footer/p2.png') }}" alt="footer">
            </div>
            <div class="anime-3 plus-anime">
                <img src="{{ asset('sbidu/assets/images/footer/p3.png') }}" alt="footer">
            </div>
            <div class="anime-5 zigzag">
                <img src="{{ asset('sbidu/assets/images/footer/c2.png') }}" alt="footer">
            </div>
            <div class="anime-6 zigzag">
                <img src="{{ asset('sbidu/assets/images/footer/c3.png') }}" alt="footer">
            </div>
            <div class="anime-7 zigzag">
                <img src="{{ asset('sbidu/assets/images/footer/c4.png') }}" alt="footer">
            </div>
</div>
        <div class="footer-top padding-bottom padding-top">
            <div class="container">
                <div class="row mb--60">
                    <div class="col-sm-6 col-lg-3" data-aos="fade-down" data-aos-duration="1000">
                        <div class="footer-widget widget-links">
                            <h5 class="title">Kategori Lelang</h5>
                            <ul class="links-list">
                                @foreach($kategoris as $data)
                                <li>
                                    <a href="{{ route('kategori.show', $data->slug) }}">{{ $data->nama }}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3" data-aos="fade-down" data-aos-duration="1300">
                        <div class="footer-widget widget-links">
                            <h5 class="title">About Us</h5>
                            <ul class="links-list">
                                <li>
                                    <a href="#0">About Sbidu</a>
                                </li>
                                <li>
                                    <a href="#0">Help</a>
                                </li>
                                <li>
                                    <a href="#0">Affiliates</a>
                                </li>
                                <li>
                                    <a href="#0">Jobs</a>
                                </li>
                                <li>
                                    <a href="#0">Press</a>
                                </li>
                                <li>
                                    <a href="#0">Our blog</a>
                                </li>
                                <li>
                                    <a href="#0">Collectors' portal</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3" data-aos="fade-down" data-aos-duration="1600">
                        <div class="footer-widget widget-links">
                            <h5 class="title">We're Here to Help</h5>
                            <ul class="links-list">
                                <li>
                                    <a href="#0">Your Account</a>
                                </li>
                                <li>
                                    <a href="#0">Safe and Secure</a>
                                </li>
                                <li>
                                    <a href="#0">Shipping Information</a>
                                </li>
                                <li>
                                    <a href="#0">Contact Us</a>
                                </li>
                                <li>
                                    <a href="#0">Help & FAQ</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3" data-aos="fade-down" data-aos-duration="1800">
                        <div class="footer-widget widget-follow">
                            <h5 class="title">Follow Us</h5>
                            <ul class="links-list">
                                <li>
                                    <a href="#0"><i class="fas fa-phone-alt"></i>(646) 663-4575</a>
                                </li>
                                <li>
                                    <a href="#0"><i class="fas fa-blender-phone"></i>(646) 968-0608</a>
                                </li>
                                <li>
                                    <a href="#0"><i class="fas fa-envelope-open-text"></i>help@engotheme.com</a>
                                </li>
                                <li>
                                    <a href="#0"><i class="fas fa-location-arrow"></i>1201 Broadway Suite</a>
                                </li>
                            </ul>
                            <ul class="social-icons">
                                <li>
                                    <a href="#0" class="active"><i class="fab fa-facebook-f"></i></a>
                                </li>
                                <li>
                                    <a href="#0"><i class="fab fa-twitter"></i></a>
                                </li>
                                <li>
                                    <a href="#0"><i class="fab fa-instagram"></i></a>
                                </li>
                                <li>
                                    <a href="#0"><i class="fab fa-linkedin-in"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="copyright-area">
                    <div class="footer-bottom-wrapper">
                        <div class="logo">
                            <a href="index.html"><img src="{{ asset('sbidu/assets/images/logo/footer-logo.png') }}" alt="logo"></a>
                        </div>
                        <ul class="gateway-area">
                            <li>
                                <a href="#0"><img src="{{ asset('sbidu/assets/images/footer/paypal.png') }}" alt="footer"></a>
                            </li>
                            <li>
                                <a href="#0"><img src="{{ asset('sbidu/assets/images/footer/visa.png') }}" alt="footer"></a>
                            </li>
                            <li>
                                <a href="#0"><img src="{{ asset('sbidu/assets/images/footer/discover.png') }}" alt="footer"></a>
                            </li>
                            <li>
                                <a href="#0"><img src="{{ asset('sbidu/assets/images/footer/mastercard.png') }}" alt="footer"></a>
                            </li>
                        </ul>
                        <div class="copyright"><p>&copy; Copyright 2024 | <a href="#0">Sbidu</a> By <a href="#0">Uiaxis</a></p></div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--============= Footer Section Ends Here =============-->



    <script src="{{ asset('sbidu/assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/modernizr-3.6.0.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/plugins.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/aos.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/waypoints.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/nice-select.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/counterup.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/owl.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/magnific-popup.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/yscountdown.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('sbidu/assets/js/main.js') }}"></script>
    @include('sweetalert::alert')
    @stacks('scripts')
</body>

</html>