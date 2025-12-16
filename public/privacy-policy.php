<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Privacy Policy';
$pageDescription = 'Privacy Policy for ' . SITE_NAME . '. Learn how we collect, use, and protect your personal information.';

include __DIR__ . '/../includes/header.php';
?>

<!-- Privacy Policy Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-wrapper">
                    <h1 class="mb-4">Privacy Policy</h1>
                    <p class="lead text-muted mb-4">
                        Last updated: <?php echo date('F j, Y'); ?>
                    </p>
                    
                    <div class="policy-content">
                        <h2>1. Introduction</h2>
                        <p>
                            Welcome to <?php echo SITE_NAME; ?>. We respect your privacy and are committed to protecting your personal information. 
                            This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website 
                            and use our AI-generated stock photo services.
                        </p>

                        <h2>2. Information We Collect</h2>
                        
                        <h3>2.1 Information You Provide</h3>
                        <ul>
                            <li>Contact information when you reach out to us via our contact form</li>
                            <li>Account information if you create an account with us</li>
                            <li>Feedback and communications with our support team</li>
                        </ul>

                        <h3>2.2 Information Automatically Collected</h3>
                        <ul>
                            <li>IP address and geolocation data</li>
                            <li>Browser type, operating system, and device information</li>
                            <li>Pages visited, time spent, and click patterns</li>
                            <li>Referring website and search terms used</li>
                            <li>Cookies and similar tracking technologies</li>
                        </ul>

                        <h3>2.3 Cookies and Tracking</h3>
                        <p>
                            We use cookies to enhance your experience, analyze site traffic, and personalize content. Our cookie banner 
                            allows you to control which types of cookies you accept:
                        </p>
                        <ul>
                            <li><strong>Essential Cookies:</strong> Required for the website to function properly</li>
                            <li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our website</li>
                            <li><strong>Marketing Cookies:</strong> Used to deliver advertisements relevant to you</li>
                        </ul>

                        <h2>3. How We Use Your Information</h2>
                        <p>We use the information we collect for the following purposes:</p>
                        <ul>
                            <li>To provide, operate, and maintain our website and services</li>
                            <li>To improve and personalize your user experience</li>
                            <li>To analyze and understand how our website is used</li>
                            <li>To develop new features and services</li>
                            <li>To communicate with you about our services</li>
                            <li>To detect, prevent, and address technical issues</li>
                            <li>To comply with legal obligations</li>
                        </ul>

                        <h2>4. Information Sharing and Disclosure</h2>
                        <p>We do not sell, trade, or otherwise transfer your personal information to third parties except in the following circumstances:</p>
                        <ul>
                            <li>With your explicit consent</li>
                            <li>To trusted service providers who assist us in operating our website</li>
                            <li>When required by law or to protect our rights</li>
                            <li>In connection with a business transfer or merger</li>
                        </ul>

                        <h2>5. Data Security</h2>
                        <p>
                            We implement appropriate technical and organizational measures to protect your personal information 
                            against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission 
                            over the internet or electronic storage is 100% secure.
                        </p>

                        <h2>6. Data Retention</h2>
                        <p>
                            We retain your personal information only as long as necessary to fulfill the purposes outlined in this 
                            Privacy Policy, unless a longer retention period is required or permitted by law.
                        </p>

                        <h2>7. Your Rights</h2>
                        <p>Depending on your location, you may have the following rights regarding your personal information:</p>
                        <ul>
                            <li>Access to your personal information</li>
                            <li>Correction of inaccurate information</li>
                            <li>Deletion of your personal information</li>
                            <li>Restriction of processing</li>
                            <li>Data portability</li>
                            <li>Objection to processing</li>
                        </ul>
                        <p>To exercise these rights, please contact us at <?php echo CONTACT_EMAIL; ?>.</p>

                        <h2>8. International Data Transfers</h2>
                        <p>
                            Your information may be transferred to and processed in countries other than your own. We ensure that 
                            appropriate safeguards are in place to protect your information in accordance with applicable data protection laws.
                        </p>

                        <h2>9. Children's Privacy</h2>
                        <p>
                            Our website is not intended for children under the age of 13. We do not knowingly collect personal 
                            information from children under 13. If you believe we have collected such information, please contact us immediately.
                        </p>

                        <h2>10. Changes to This Privacy Policy</h2>
                        <p>
                            We may update this Privacy Policy from time to time. We will notify you of any changes by posting the 
                            new Privacy Policy on this page and updating the "Last updated" date at the top.
                        </p>

                        <h2>11. Contact Us</h2>
                        <p>
                            If you have any questions about this Privacy Policy or our data practices, please contact us:
                        </p>
                        <ul>
                            <li>Email: <?php echo CONTACT_EMAIL; ?></li>
                            <li>Website: <a href="/contact.php">Contact Form</a></li>
                        </ul>

                        <h2>12. AI-Generated Content Disclaimer</h2>
                        <p>
                            Please note that all images on our platform are AI-generated and do not depict real individuals, places, or events. 
                            No personal data is used in the generation of these images, and they are created using machine learning algorithms 
                            trained on publicly available datasets.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.content-wrapper {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
    padding: 40px;
    margin-bottom: 30px;
}

.policy-content h2 {
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
    margin-top: 40px;
    margin-bottom: 20px;
}

.policy-content h3 {
    color: #555;
    margin-top: 30px;
    margin-bottom: 15px;
}

.policy-content ul, .policy-content p {
    color: #666;
    line-height: 1.8;
    margin-bottom: 20px;
}

.policy-content ul li {
    margin-bottom: 10px;
}

.policy-content a {
    color: #667eea;
    text-decoration: none;
}

.policy-content a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .content-wrapper {
        padding: 25px;
        margin: 15px;
    }
    
    .policy-content h2 {
        font-size: 1.4rem;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>