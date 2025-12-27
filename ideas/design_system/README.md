# Solar ERP Design System

A comprehensive design system for the Solar Panel & Electrical EPC accounting application.

## Overview

This design system is built for Indonesian EPC (Engineering, Procurement, Construction) companies specializing in:
- Solar panel installation
- Electrical panel manufacturing (LVMDP, MDP, etc.)
- Project-based contracting

## Design Philosophy

### 1. Value-First Design
Every screen emphasizes what matters most to the user. Dashboards show actionable metrics, not just data.

### 2. Project-Centric
EPC businesses revolve around projects. The UI reflects this by connecting all elements (costs, materials, labor, invoices) back to projects.

### 3. Action-Oriented
Alerts and notifications always include direct action buttons. Users should never see a problem without a clear path to resolution.

### 4. Progressive Disclosure
Show summary first, details on demand. Reduce cognitive load by revealing complexity only when needed.

### 5. Indonesian Context
- Currency: Rupiah (Rp) with dot separators (Rp 1.250.000)
- Tax: PPN 11% clearly displayed
- Dates: DD MMM YYYY format
- Mixed Indonesian/English terminology where appropriate

## File Structure

```
design_system/
├── README.md                    # This file
├── 01-foundations.md            # Colors, typography, spacing, icons
├── 02-components-core.md        # Buttons, inputs, cards, badges
├── 03-components-data.md        # Tables, stats, charts, progress
├── 04-components-navigation.md  # Sidebar, breadcrumbs, tabs, search
├── 05-components-epc.md         # Project cards, work orders, BOM
├── 06-patterns.md               # Layouts, forms, modals, workflows
└── 07-tokens.md                 # CSS/Tailwind design tokens
```

## Tech Stack Recommendations

### Frontend Framework
- **Vue 3** with Composition API
- **TypeScript** for type safety
- **Inertia.js** (optional, for Laravel integration)

### Styling
- **Tailwind CSS** for utility-first styling
- **Headless UI** for accessible components
- **Heroicons** for iconography

### State Management
- **Pinia** for global state
- **TanStack Query** for server state/caching

### Charts & Visualization
- **Chart.js** or **Apache ECharts** for financial charts
- **D3.js** for complex visualizations (optional)

## Design Tokens

Design tokens are defined in `07-tokens.md` and can be exported to:
- Tailwind CSS config
- CSS custom properties
- JSON for design tools

## User Roles & Views

| Role | Primary View | Key Features |
|------|-------------|--------------|
| Owner/Management | Executive Dashboard | KPIs, cash flow, project margins |
| Sales | Quotation Pipeline | Quote creation, conversion tracking |
| Project Manager | Project Dashboard | Progress, costs, work orders |
| Procurement | MRP Planning | Shortages, PO management |
| Warehouse | Stock Overview | Inventory, material requisitions |
| Finance | AR/AP Dashboards | Aging, payments, reports |
| Admin | Settings | Users, roles, system config |

## Accessibility

This design system follows WCAG 2.1 AA guidelines:
- Minimum contrast ratio 4.5:1 for text
- Focus indicators on all interactive elements
- Keyboard navigation support
- Screen reader friendly labels

## Responsive Breakpoints

| Breakpoint | Width | Use Case |
|------------|-------|----------|
| `sm` | 640px | Mobile landscape |
| `md` | 768px | Tablet portrait |
| `lg` | 1024px | Tablet landscape / Small laptop |
| `xl` | 1280px | Desktop |
| `2xl` | 1536px | Large desktop |

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | Dec 2024 | Initial design system |
