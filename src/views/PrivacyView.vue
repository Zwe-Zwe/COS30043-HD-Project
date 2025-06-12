<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="mb-5 text-center">
          <h1>Privacy Policy</h1>
          <p class="lead text-muted">Last Updated: {{ lastUpdated }}</p>
        </div>
        
        <div class="privacy-content">
          <section class="mb-5">
            <h2>1. Introduction</h2>
            <p>BookNest ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or make a purchase.</p>
            <p>Please read this Privacy Policy carefully. If you disagree with our practices, please discontinue use of our services. By accessing our website, you accept our Privacy Policy and Terms of Service.</p>
          </section>
          
          <section class="mb-5">
            <h2>2. Information We Collect</h2>
            
            <h3>2.1 Personal Information</h3>
            <p>We may collect personal information that you provide directly to us when you:</p>
            <ul>
              <li>Create an account</li>
              <li>Make a purchase</li>
              <li>Sign up for our newsletter</li>
              <li>Contact our customer service</li>
              <li>Participate in surveys or promotions</li>
            </ul>
            
            <p>This information may include:</p>
            <ul>
              <li>Name</li>
              <li>Email address</li>
              <li>Mailing address</li>
              <li>Phone number</li>
              <li>Payment information (processed securely through our payment providers)</li>
              <li>Purchase history</li>
            </ul>
            
            <h3>2.2 Automatically Collected Information</h3>
            <p>When you visit our website, we automatically collect certain information about your device and usage, including:</p>
            <ul>
              <li>IP address</li>
              <li>Browser type and version</li>
              <li>Operating system</li>
              <li>Referring website</li>
              <li>Pages visited and time spent</li>
              <li>Device information</li>
            </ul>
            
            <h3>2.3 Cookies and Similar Technologies</h3>
            <p>We use cookies, web beacons, and similar technologies to improve your browsing experience, personalize content, and analyze site traffic. You can manage cookie preferences through your browser settings.</p>
          </section>
          
          <section class="mb-5">
            <h2>3. How We Use Your Information</h2>
            <p>We may use the information we collect for various purposes, including to:</p>
            <ul>
              <li>Process and fulfill your orders</li>
              <li>Create and manage your account</li>
              <li>Send transactional emails and order confirmations</li>
              <li>Provide customer support</li>
              <li>Send marketing and promotional communications (with your consent)</li>
              <li>Personalize your experience</li>
              <li>Analyze usage and improve our services</li>
              <li>Detect and prevent fraud</li>
              <li>Comply with legal obligations</li>
            </ul>
          </section>
          
          <section class="mb-5">
            <h2>4. Information Sharing</h2>
            <p>We may share your information with:</p>
            <ul>
              <li><strong>Service Providers:</strong> Third parties who perform services on our behalf (payment processing, shipping, email services, etc.)</li>
              <li><strong>Business Partners:</strong> With your consent, we may share information with business partners to offer you certain products or services</li>
              <li><strong>Legal Requirements:</strong> When required by law or to respond to legal process</li>
              <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
            </ul>
            <p>We do not sell your personal information to third parties for marketing purposes.</p>
          </section>
          
          <section class="mb-5">
            <h2>5. Data Security</h2>
            <p>We implement appropriate technical and organizational measures to protect your information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure. We cannot guarantee absolute security.</p>
          </section>
          
          <section class="mb-5">
            <h2>6. Your Rights and Choices</h2>
            <p>Depending on your location, you may have certain rights regarding your personal information, including:</p>
            <ul>
              <li>Access to your personal information</li>
              <li>Correction of inaccurate or incomplete information</li>
              <li>Deletion of your personal information</li>
              <li>Restriction of processing</li>
              <li>Data portability</li>
              <li>Objection to processing</li>
            </ul>
            <p>To exercise these rights, please contact us using the information provided in the "Contact Us" section.</p>
          </section>
          
          <section class="mb-5">
            <h2>7. Children's Privacy</h2>
            <p>Our services are not directed to individuals under the age of 13. We do not knowingly collect personal information from children under 13. If you become aware that a child has provided us with personal information, please contact us.</p>
          </section>
          
          <section class="mb-5">
            <h2>8. Updates to This Privacy Policy</h2>
            <p>We may update this Privacy Policy from time to time. The updated version will be indicated by an updated "Last Updated" date. We encourage you to review this Privacy Policy periodically.</p>
          </section>
          
          <section class="mb-5">
            <h2>9. Contact Us</h2>
            <p>If you have questions or comments about this Privacy Policy, please contact us at:</p>
            <address class="ms-4">
              <strong>BookNest</strong><br>
              123 Book Street<br>
              Melbourne, VIC 3000<br>
              Australia<br>
              Email: privacy@booknest.com<br>
              Phone: 1800 BOOKS (26657)
            </address>
          </section>
        </div>
        
        <div class="text-center mt-5 pt-3 border-top">
          <router-link to="/contact" class="btn btn-primary mt-2">
            Contact Our Privacy Team
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PrivacyView',
  data() {
    return {
      lastUpdated: 'June 1, 2023'
    };
  }
};
</script>

<style scoped>
.privacy-content h2 {
  color: #0d6efd;
  margin-top: 2rem;
  margin-bottom: 1rem;
  font-size: 1.75rem;
}

.privacy-content h3 {
  font-size: 1.3rem;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}

.privacy-content p,
.privacy-content li {
  color: #495057;
  line-height: 1.6;
}

.privacy-content ul {
  margin-bottom: 1.5rem;
}

.privacy-content section {
  padding-bottom: 1rem;
  margin-bottom: 2rem;
  border-bottom: 1px solid #e9ecef;
}
</style>
