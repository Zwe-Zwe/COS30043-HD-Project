<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="mb-5 text-center">
          <h1>Terms of Use</h1>
          <p class="lead text-muted">Last Updated: {{ lastUpdated }}</p>
        </div>
        
        <div class="terms-content">
          <section class="mb-5">
            <h2>1. Introduction</h2>
            <p>Welcome to BookNest. These Terms of Use govern your use of our website located at <a href="/">www.booknest.com</a> (the "Service") operated by BookNest Pty Ltd ("us", "we", or "our").</p>
            <p>By accessing or using the Service, you agree to be bound by these Terms. If you disagree with any part of the terms, then you may not access the Service.</p>
          </section>
          
          <section class="mb-5">
            <h2>2. Accounts</h2>
            <p>When you create an account with us, you must provide information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account.</p>
            <p>You are responsible for safeguarding the password you use to access the Service and for any activities or actions under your password. You agree not to disclose your password to any third party. You must notify us immediately upon becoming aware of any breach of security or unauthorized use of your account.</p>
          </section>
          
          <section class="mb-5">
            <h2>3. Purchases</h2>
            <p>If you wish to purchase any product or service made available through the Service ("Purchase"), you may be asked to supply certain information relevant to your Purchase including, without limitation, your credit card number, the expiration date of your credit card, your billing address, and your shipping information.</p>
            <p>You represent and warrant that: (i) you have the legal right to use any credit card(s) or other payment method(s) in connection with any Purchase; and that (ii) the information you supply to us is true, correct and complete.</p>
            <p>We reserve the right to refuse or cancel your order at any time for reasons including but not limited to: product or service availability, errors in the description or price of the product or service, error in your order or other reasons.</p>
          </section>
          
          <section class="mb-5">
            <h2>4. Availability and Pricing</h2>
            <p>We are constantly updating our product offerings on the Service. We reserve the right to discontinue any product at any time. Any offer for any product or service made on this Service is void where prohibited.</p>
            <p>We do not warrant that the quality of any products, services, information, or other material purchased or obtained by you will meet your expectations, or that any errors in the Service will be corrected.</p>
            <p>Prices for our products are subject to change without notice. We reserve the right at any time to modify or discontinue the Service (or any part or content thereof) without notice at any time.</p>
          </section>
          
          <section class="mb-5">
            <h2>5. Intellectual Property Rights</h2>
            <p>The Service and its original content, features, and functionality are and will remain the exclusive property of BookNest and its licensors. The Service is protected by copyright, trademark, and other laws of both Australia and foreign countries. Our trademarks and trade dress may not be used in connection with any product or service without the prior written consent of BookNest.</p>
          </section>
          
          <section class="mb-5">
            <h2>6. User Content</h2>
            <p>Our Service may allow you to post, link, store, share and otherwise make available certain information, text, graphics, videos, or other material ("User Content"). You are responsible for the User Content that you post to the Service, including its legality, reliability, and appropriateness.</p>
            <p>By posting User Content to the Service, you grant us the right and license to use, modify, publicly perform, publicly display, reproduce, and distribute such content on and through the Service. You retain any and all of your rights to any Content you submit, post or display on or through the Service and you are responsible for protecting those rights.</p>
          </section>
          
          <section class="mb-5">
            <h2>7. Limitation of Liability</h2>
            <p>In no event shall BookNest, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from (i) your access to or use of or inability to access or use the Service; (ii) any conduct or content of any third party on the Service; (iii) any content obtained from the Service; and (iv) unauthorized access, use or alteration of your transmissions or content.</p>
          </section>
          
          <section class="mb-5">
            <h2>8. Disclaimer</h2>
            <p>Your use of the Service is at your sole risk. The Service is provided on an "AS IS" and "AS AVAILABLE" basis. The Service is provided without warranties of any kind, whether express or implied, including, but not limited to, implied warranties of merchantability, fitness for a particular purpose, non-infringement or course of performance.</p>
          </section>
          
          <section class="mb-5">
            <h2>9. Governing Law</h2>
            <p>These Terms shall be governed and construed in accordance with the laws of Australia, without regard to its conflict of law provisions.</p>
            <p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect.</p>
          </section>
          
          <section class="mb-5">
            <h2>10. Changes</h2>
            <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>
            <p>By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, please stop using the Service.</p>
          </section>
          
          <section class="mb-5">
            <h2>11. Contact Us</h2>
            <p>If you have any questions about these Terms, please contact us at:</p>
            <address class="ms-4">
              <strong>BookNest</strong><br>
              123 Book Street<br>
              Melbourne, VIC 3000<br>
              Australia<br>
              Email: legal@booknest.com<br>
              Phone: 1800 BOOKS (26657)
            </address>
          </section>
        </div>
        
        <div class="text-center mt-5 pt-3 border-top">
          <router-link to="/contact" class="btn btn-primary mt-2">
            Contact Our Legal Team
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TermsView',
  data() {
    return {
      lastUpdated: 'June 1, 2023'
    };
  }
};
</script>

<style scoped>
.terms-content h2 {
  color: #0d6efd;
  margin-top: 2rem;
  margin-bottom: 1rem;
  font-size: 1.75rem;
}

.terms-content h3 {
  font-size: 1.3rem;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}

.terms-content p,
.terms-content li {
  color: #495057;
  line-height: 1.6;
}

.terms-content ul {
  margin-bottom: 1.5rem;
}

.terms-content section {
  padding-bottom: 1rem;
  margin-bottom: 2rem;
  border-bottom: 1px solid #e9ecef;
}
</style>
