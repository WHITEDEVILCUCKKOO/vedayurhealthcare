  <!-- section 7 -->
  <section class="contact" id="contact">
      <div class="contact-copy">
          <h2>Ready to Begin Your<br />Wellness Journey?</h2>
          <p>Get in touch with us for product details, guidance or treatment information.</p>
          <p>We have been serving wellness needs since 2009.</p>
          <div class="since">✦ Since 2009<br /><small style="color:#ddd">Trusted for a Healthier Tomorrow</small>
          </div>
      </div>

      <!-- Section 7: Contact Form -->
      <form class="contact-form" id="contactForm" novalidate>
          <h3>Contact Vedayur Healthcare</h3>
          <p>Fill in your details and our team will contact you.</p>

          <div class="contact-fields">

              <div class="field">
                  <label for="contactName">Name <span class="required-star">*</span></label>
                  <input type="text" id="contactName" name="name" placeholder="Your Name" autocomplete="name"
                      maxlength="80" required>
                  <small class="contact-error" id="contactNameError"></small>
              </div>

              <div class="field">
                  <label for="contactPhone">Number <span class="required-star">*</span></label>
                  <input type="tel" id="contactPhone" name="phone" inputmode="numeric" maxlength="10"
                      placeholder="Your Phone Number" autocomplete="tel" required>
                  <small class="contact-error" id="contactPhoneError"></small>
              </div>

              <div class="field">
                  <label for="contactArea">Area <span class="required-star">*</span></label>
                  <input type="text" id="contactArea" name="area" placeholder="Your Area"
                      autocomplete="address-level2" maxlength="100" required>
                  <small class="contact-error" id="contactAreaError"></small>
              </div>

              <div class="field">
                  <label for="contactPincode">Pincode <span class="required-star">*</span></label>
                  <input type="text" id="contactPincode" name="pincode" inputmode="numeric" maxlength="6"
                      placeholder="6-digit Pincode" autocomplete="postal-code" required>
                  <small class="contact-error" id="contactPincodeError"></small>
              </div>

          </div>

          <button class="submit" type="submit" id="contactSubmit">
              ➤ &nbsp; Send Details
          </button>

          <div class="safe">🔒 Your information is safe with us.</div>
      </form>

  </section>