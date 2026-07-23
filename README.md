## Zenserp Integration

Given that Netwatch Global is a leading business in turning public data into decisions fast I decided to use Zenserp to pull WEB information.

### General Idea

Provide a "Internal" search platform via Zenserp that could be logged and recorded.

### Feature Ideas

I decided to decouple the upstream from the application so if the upstream contract changes, it could be resolved without modification to the UI - this creates a boundary between the UI and server and server to upstream.

Given more time, I'd of more generally implemented the integration into a service so it could be easily swapped out/configured.

I added a simple Rate Limiting feature and given more time, I'd of added some migrations for search logging and the ability to add regex alerts on searches.