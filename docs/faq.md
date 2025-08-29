# FAQ

**Q:** Do I need Redis?  
**A:** Yes—for queues, cache, and broadcasts.

**Q:** Can I disable websockets locally?  
**A:** Yes, switch broadcast driver to `log` or `null`.

**Q:** How do I add a new tenant?  
**A:** Use the central dashboard; it provisions DNS/subdomain and seeds defaults.
