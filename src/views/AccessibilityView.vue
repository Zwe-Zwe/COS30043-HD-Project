<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="mb-5 text-center">
          <h1>Accessibility Statement</h1>
          <p class="lead text-muted">Last Updated: {{ lastUpdated }}</p>
        </div>
        
        <div class="accessibility-content">
          <section class="mb-5">
            <h2>Our Commitment to Accessibility</h2>
            <p>BookNest is committed to ensuring digital accessibility for people with disabilities. We are continually improving the user experience for everyone, and applying the relevant accessibility standards.</p>
          </section>
          
          <section class="mb-5">
            <h2>Conformance Status</h2>
            <p>The Web Content Accessibility Guidelines (WCAG) define requirements for designers and developers to improve accessibility for people with disabilities. It defines three levels of conformance: Level A, Level AA, and Level AAA.</p>
            <p>BookNest is partially conformant with WCAG 2.1 level AA. Partially conformant means that some parts of the content do not fully conform to the accessibility standard.</p>
          </section>
          
          <section class="mb-5">
            <h2>Accessibility Features</h2>
            <p>BookNest includes the following accessibility features:</p>
            <ul>
              <li><strong>Text Alternatives:</strong> We provide text alternatives for non-text content.</li>
              <li><strong>Keyboard Navigation:</strong> All functionality is available from a keyboard.</li>
              <li><strong>Reading Order:</strong> Content is presented in a meaningful sequence.</li>
              <li><strong>Color Contrast:</strong> Text displays with sufficient contrast.</li>
              <li><strong>Resizable Text:</strong> Text can be resized without assistive technology up to 200% without loss of content or functionality.</li>
              <li><strong>Consistent Navigation:</strong> Navigation mechanisms that are repeated throughout the website occur in the same relative order.</li>
            </ul>
          </section>
          
          <section class="mb-5">
            <h2>Assistive Technology Compatibility</h2>
            <p>BookNest is designed to be compatible with assistive technologies and the browsers that they support, including:</p>
            <ul>
              <li>Screen readers (JAWS, NVDA, VoiceOver, TalkBack)</li>
              <li>Screen magnification software</li>
              <li>Speech recognition software</li>
              <li>Keyboard-only navigation</li>
            </ul>
          </section>
          
          <section class="mb-5">
            <h2>Known Limitations</h2>
            <p>Despite our best efforts to ensure accessibility of BookNest, there may be some limitations. Below is a description of known limitations, and potential solutions. Please contact us if you observe an issue not listed below.</p>
            <ul>
              <li><strong>Some older PDF documents:</strong> Some of our older PDF documents may not be completely accessible. We are working on updating these documents.</li>
              <li><strong>Third-party applications:</strong> Some pages may include third-party applications that are outside of our control and may not be fully accessible.</li>
            </ul>
          </section>
          
          <section class="mb-5">
            <h2>Accessibility Improvements in Progress</h2>
            <p>BookNest is committed to continually improving accessibility for all users. We are actively working to increase the accessibility and usability of our website and in doing so adhere to many of the available standards and guidelines.</p>
            <p>Our ongoing accessibility improvements include:</p>
            <ul>
              <li>Regular accessibility audits and testing</li>
              <li>Staff training on digital accessibility</li>
              <li>Incorporation of accessibility feedback from users</li>
              <li>Engaging with the community to gather feedback on our accessibility features</li>
            </ul>
          </section>
          
          <section class="mb-5">
            <h2>Feedback</h2>
            <p>We welcome your feedback on the accessibility of BookNest. Please let us know if you encounter accessibility barriers on our website:</p>
            <ul>
              <li><strong>Email:</strong> accessibility@booknest.com</li>
              <li><strong>Phone:</strong> 1800 BOOKS (26657)</li>
              <li><strong>Postal Address:</strong> BookNest, 123 Book Street, Melbourne, VIC 3000, Australia</li>
            </ul>
            <p>We try to respond to feedback within 2 business days.</p>
          </section>
        </div>
        
        <div class="text-center mt-5 pt-3 border-top">
          <h4>Questions or suggestions about accessibility?</h4>
          <router-link to="/contact" class="btn btn-primary mt-2">
            Contact Our Accessibility Team
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AccessibilityView',
  data() {
    return {
      lastUpdated: 'June 15, 2023'
    };
  }
};
</script>

<style scoped>
.accessibility-content h2 {
  color: #0d6efd;
  margin-top: 2rem;
  margin-bottom: 1rem;
  font-size: 1.75rem;
}

.accessibility-content p,
.accessibility-content li {
  color: #495057;
  line-height: 1.6;
}

.accessibility-content ul {
  margin-bottom: 1.5rem;
}

.accessibility-content section {
  padding-bottom: 1rem;
  margin-bottom: 2rem;
  border-bottom: 1px solid #e9ecef;
}
</style>
