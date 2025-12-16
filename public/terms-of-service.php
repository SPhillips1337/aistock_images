<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Terms of Service';
$pageDescription = 'Terms of Service for ' . SITE_NAME . '. Read our terms and conditions for using our AI-generated stock photo services.';

include __DIR__ . '/../includes/header.php';
?>

<!-- Terms of Service Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-wrapper">
                    <h1 class="mb-4">Terms of Service</h1>
                    <p class="lead text-muted mb-4">
                        Last updated: <?php echo date('F j, Y'); ?>
                    </p>
                    
                    <div class="terms-content">
                        <h2>1. Acceptance of Terms</h2>
                        <p>
                            By accessing and using <?php echo SITE_NAME; ?>, you accept and agree to be bound by the terms and 
                            conditions of this agreement. If you do not agree to abide by the above, please do not use this service.
                        </p>

                        <h2>2. Description of Service</h2>
                        <p>
                            <?php echo SITE_NAME; ?> is a platform that provides AI-generated stock photos for various purposes. 
                            All images on our platform are created using artificial intelligence and do not depict real individuals, 
                            places, or events unless explicitly stated.
                        </p>

                        <h2>3. User Accounts</h2>
                        
                        <h3>3.1 Registration</h3>
                        <p>
                            While most of our services are available without registration, certain features may require creating an account. 
                            You are responsible for maintaining the confidentiality of your account credentials.
                        </p>

                        <h3>3.2 Account Responsibilities</h3>
                        <p>You agree to:</p>
                        <ul>
                            <li>Provide accurate and complete information</li>
                            <li>Keep your password secure</li>
                            <li>Accept responsibility for all activities under your account</li>
                            <li>Notify us immediately of any unauthorized use</li>
                        </ul>

                        <h2>4. Permitted Uses</h2>
                        <p>You may use images downloaded from our platform for:</p>
                        <ul>
                            <li>Personal and commercial projects</li>
                            <li>Website design and development</li>
                            <li>Social media content</li>
                            <li>Marketing and advertising materials</li>
                            <li>Presentations and educational materials</li>
                            <li>Printed materials (books, magazines, brochures)</li>
                        </ul>

                        <h2>5. Prohibited Uses</h2>
                        <p>You may NOT use images from our platform to:</p>
                        <ul>
                            <li>Represent real individuals, companies, or products without permission</li>
                            <li>Create misleading or deceptive content</li>
                            <li>Violate any applicable laws or regulations</li>
                            <li>Harass, defame, or infringe on the rights of others</li>
                            <li>Create offensive, illegal, or harmful content</li>
                            <li>Resell or redistribute images as standalone products</li>
                            <li>Use images in trademarks or service marks</li>
                        </ul>

                        <h2>6. Intellectual Property</h2>
                        
                        <h3>6.1 Platform Content</h3>
                        <p>
                            The website, including its design, graphics, text, and software, is owned by or licensed to <?php echo SITE_NAME; ?> 
                            and is protected by intellectual property laws.
                        </p>

                        <h3>6.2 AI-Generated Images</h3>
                        <p>
                            Images on our platform are AI-generated and provided to you under a non-exclusive, non-transferable license 
                            to use the images in accordance with these terms. The AI models used to generate these images were trained on 
                            publicly available datasets and do not reproduce any specific copyrighted work.
                        </p>

                        <h2>7. User Content</h2>
                        
                        <h3>7.1 Submissions</h3>
                        <p>
                            If you submit content to our platform (such as feedback, reviews, or suggestions), you grant us a 
                            non-exclusive, worldwide, perpetual license to use, modify, and distribute such content.
                        </p>

                        <h3>7.2 User Responsibilities</h3>
                        <p>You are solely responsible for any content you submit and must ensure it:</p>
                        <ul>
                            <li>Does not infringe on third-party rights</li>
                            <li>Is not unlawful, harmful, or offensive</li>
                            <li>Does not contain malicious code or viruses</li>
                        </ul>

                        <h2>8. Privacy</h2>
                        <p>
                            Your privacy is important to us. Please review our Privacy Policy, which also governs your use 
                            of the service, to understand our practices.
                        </p>

                        <h2>9. Disclaimers</h2>
                        
                        <h3>9.1 AI-Generated Content</h3>
                        <p>
                            All images on our platform are AI-generated and may contain imperfections, inaccuracies, or unexpected elements. 
                            We do not guarantee that images will perfectly match your requirements or expectations.
                        </p>

                        <h3>9.2 Service Availability</h3>
                        <p>
                            We strive to maintain our service availability but do not guarantee uninterrupted access. 
                            We reserve the right to modify, suspend, or discontinue the service at any time.
                        </p>

                        <h2>10. Limitation of Liability</h2>
                        <p>
                            To the maximum extent permitted by law, <?php echo SITE_NAME; ?> shall not be liable for any indirect, 
                            incidental, special, consequential, or punitive damages, including but not limited to loss of profits, 
                            data, use, or goodwill, arising from your use of the service.
                        </p>

                        <h2>11. Indemnification</h2>
                        <p>
                            You agree to indemnify and hold <?php echo SITE_NAME; ?> harmless from any claims, damages, or expenses 
                            arising from your use of the service or violation of these terms.
                        </p>

                        <h2>12. Termination</h2>
                        <p>
                            We may terminate or suspend your account and bar access to the service immediately, without prior notice 
                            or liability, under our sole discretion, for any reason whatsoever, including but not limited to a breach of the Terms.
                        </p>

                        <h2>13. Changes to Terms</h2>
                        <p>
                            We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting. 
                            Your continued use of the service constitutes acceptance of the modified terms.
                        </p>

                        <h2>14. Governing Law</h2>
                        <p>
                            These Terms shall be interpreted and governed by the laws of the jurisdiction in which <?php echo SITE_NAME; ?> operates, 
                            without regard to conflict of law provisions.
                        </p>

                        <h2>15. Contact Information</h2>
                        <p>
                            If you have any questions about these Terms of Service, please contact us at:
                        </p>
                        <ul>
                            <li>Email: <?php echo CONTACT_EMAIL; ?></li>
                            <li>Website: <a href="/contact.php">Contact Form</a></li>
                        </ul>

                        <h2>16. Severability</h2>
                        <p>
                            If any provision of these Terms is found to be unenforceable or invalid, that provision will be limited 
                            or eliminated to the minimum extent necessary so that the remaining Terms will remain in full force and effect.
                        </p>

                        <h2>17. Entire Agreement</h2>
                        <p>
                            These Terms constitute the entire agreement between you and <?php echo SITE_NAME; ?> regarding the use of the service, 
                            superseding any prior agreements between you and <?php echo SITE_NAME; ?>.
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

.terms-content h2 {
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
    margin-top: 40px;
    margin-bottom: 20px;
}

.terms-content h3 {
    color: #555;
    margin-top: 30px;
    margin-bottom: 15px;
}

.terms-content ul, .terms-content p {
    color: #666;
    line-height: 1.8;
    margin-bottom: 20px;
}

.terms-content ul li {
    margin-bottom: 10px;
}

.terms-content a {
    color: #667eea;
    text-decoration: none;
}

.terms-content a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .content-wrapper {
        padding: 25px;
        margin: 15px;
    }
    
    .terms-content h2 {
        font-size: 1.4rem;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>