# vinhetas.pt

WordPress theme and partner-enquiry plugin for [vinhetas.pt](https://vinhetas.pt), hosted on DreamHost. Every push to `main` deploys them automatically through GitHub Actions.

```
wp-content/themes/vinhetas/              Vinhetas Algarve theme (home page, styles, Customizer image slots)
wp-content/plugins/vinhetas-enquiries/   Partner enquiry form, stored in WordPress, CSV export
.github/workflows/deploy.yml             Copies both to DreamHost over SSH
```

Only these two folders are deployed. WordPress core, uploads, other plugins and the database are never touched.

## One-time DreamHost connection (about 15 minutes)

### 1. Turn on SSH

DreamHost panel → **Websites → SFTP Users & Files**. Find the user that owns vinhetas.pt and set its type to **SSH**. Note the **username**, the **server name** (for example `vps12345.dreamhostps.com`) and the site folder. Changes take about 5 minutes.

### 2. Make a deploy key (Terminal on your Mac)

Replace `USER` and `HOST` with the values from step 1. Run one at a time.

```sh
# Create the key (separate from the Tote Couture key)
ssh-keygen -t ed25519 -f ~/.ssh/vinhetas_deploy -N "" -C "github-deploy-vinhetas"

# Give DreamHost the public half (asks for the DreamHost user's password once)
ssh-copy-id -i ~/.ssh/vinhetas_deploy.pub USER@HOST

# Test: should log in without a password and list vinhetas.pt
ssh -i ~/.ssh/vinhetas_deploy USER@HOST "pwd; ls"

# Copy the private key to the clipboard
pbcopy < ~/.ssh/vinhetas_deploy
```

If `ssh-copy-id` says "command not found":

```sh
cat ~/.ssh/vinhetas_deploy.pub | ssh USER@HOST "mkdir -p ~/.ssh && chmod 700 ~/.ssh && cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"
```

The private key is a password. Paste it only into GitHub.

### 3. Add four secrets in GitHub

Repo → **Settings → Secrets and variables → Actions → New repository secret**:

| Name | Value |
|---|---|
| `DH_HOST` | server name, e.g. `vps12345.dreamhostps.com` |
| `DH_USER` | the SSH username |
| `DH_SITE_DIR` | the site folder, usually `/home/USER/vinhetas.pt` (the first line of the test output, then `/vinhetas.pt`) |
| `DH_SSH_KEY` | paste the private key; starts `-----BEGIN OPENSSH PRIVATE KEY-----` |

### 4. Deploy and switch on

**Actions → Deploy to DreamHost → Run workflow.** Then in WordPress (`vinhetas.pt/wp-admin`):

- **Appearance → Themes:** activate **Vinhetas Algarve**.
- **Plugins:** activate **Vinhetas Enquiries**.

Or over SSH: `cd ~/vinhetas.pt && wp theme activate vinhetas && wp plugin activate vinhetas-enquiries`

## Email and spam protection (settings in wp-config.php, never in GitHub)

vinhetas.pt mail is hosted at Proton, and the domain's SPF/DMARC only trust Proton. So the site sends its email through Proton SMTP. Set these on the server with WP-CLI (replace the token values):

```sh
ssh -i ~/.ssh/vinhetas_deploy vinhetas@vps35338.dreamhostps.com
cd ~/vinhetas.pt
wp config set VE_SMTP_USER ola@vinhetas.pt
wp config set VE_SMTP_TOKEN 'PASTE-PROTON-SMTP-TOKEN'
wp config set VE_TURNSTILE_SITE_KEY 'PASTE-SITE-KEY'
wp config set VE_TURNSTILE_SECRET 'PASTE-SECRET-KEY'
```

- **Proton token:** Proton Mail → Settings → All settings → IMAP/SMTP → SMTP tokens → Generate token, for ola@vinhetas.pt.
- **Turnstile keys:** Cloudflare dashboard → Turnstile → Add widget, hostname `vinhetas.pt` (and `www.vinhetas.pt`), widget mode **Invisible**.

Each feature switches on only when its settings are present. With Turnstile on, nothing shows on the page; the check runs when Send is clicked. Enquiries sent without the check (script blocked) are kept and marked "[Unverified]".

## Day to day

- **Photos and renderings:** Appearance → Customize → **Vinhetas media** (hero photo, three site photos, campus rendering). Empty slots show a grey placeholder with the size they need. Images live in WordPress, not GitHub, so deploys never remove them.
- **Enquiries:** the **Enquiries** menu in WordPress lists every submission; **Download CSV** exports them. New enquiries are emailed to the address under Settings → General → *Enquiry notifications* (defaults to the admin email).
- **Don't edit theme files inside WordPress** (Theme File Editor). The next deploy overwrites them. Change them here instead.
