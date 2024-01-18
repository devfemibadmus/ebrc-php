import matplotlib.pyplot as plt
import numpy as np

# Set the background color
fig, ax = plt.subplots(facecolor=(60/255, 57/255, 70/255))

# Set the text
text = 'ebrc'
plt.text(0.5, 0.5, text, fontsize=50, color='white', ha='center', va='center')

# Remove the axes
ax.axis('off')

# Save the image
plt.savefig('ebrc.png', dpi=300, bbox_inches='tight')

# Display the image
plt.show()
