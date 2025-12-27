# Killer Features for Electrical Panel Maker Company

## Feature A: Alternative BoM & Multi-Brand Quotation
For: Electrical Panel Manufacturing

  ---
  The Problem Today

  Customer asks: "Give me a quote for a 400A main panel"

  Sales person must:
  1. Create Quotation A with ABB components     → Rp 85 juta
  2. Create Quotation B with Siemens components → Rp 82 juta
  3. Create Quotation C with Schneider components → Rp 88 juta

  ❌ 3x the work
  ❌ Prone to errors (forgot to change one component)
  ❌ No easy comparison view
  ❌ BoM structure duplicated 3 times

  ---
  The Solution: Product Alternatives + Smart Quotation

  Workflow Overview:

  ┌─────────────────────────────────────────────────────────────────────────┐
  │                         SETUP (One-Time)                                │
  ├─────────────────────────────────────────────────────────────────────────┤
  │                                                                         │
  │  1. Define Product Alternatives (Equivalency Groups)                    │
  │                                                                         │
  │     ┌─────────────────────────────────────────────────────────────┐     │
  │     │  Equivalency Group: "MCCB 400A 3P"                          │     │
  │     ├─────────────────────────────────────────────────────────────┤     │
  │     │  Brand      │ SKU          │ Price      │ Lead Time │ Note  │     │
  │     │  ABB        │ MCCB-ABB-400 │ Rp 4.500k  │ 3 days    │ Stock │     │
  │     │  Siemens    │ MCCB-SIE-400 │ Rp 4.200k  │ 7 days    │ PO    │     │
  │     │  Schneider  │ MCCB-SCH-400 │ Rp 4.800k  │ 5 days    │ Stock │     │
  │     │  Chint      │ MCCB-CHT-400 │ Rp 2.100k  │ 2 days    │ Stock │     │
  │     └─────────────────────────────────────────────────────────────┘     │
  │                                                                         │
  │  2. Create Master BoM with "Generic" Components                         │
  │                                                                         │
  │     ┌─────────────────────────────────────────────────────────────┐     │
  │     │  BoM: Panel Distribusi 400A                                 │     │
  │     ├─────────────────────────────────────────────────────────────┤     │
  │     │  Line │ Component              │ Qty │ Equiv. Group         │     │
  │     │  1    │ MCCB 400A 3P           │ 1   │ "MCCB 400A 3P"       │     │
  │     │  2    │ MCCB 100A 3P           │ 4   │ "MCCB 100A 3P"       │     │
  │     │  3    │ MCB 16A 1P             │ 12  │ "MCB 16A 1P"         │     │
  │     │  4    │ Busbar Copper 400A     │ 1   │ null (no alt)        │     │
  │     │  5    │ Enclosure 800x600x250  │ 1   │ null (no alt)        │     │
  │     │  6    │ Wiring & Accessories   │ 1   │ null (no alt)        │     │
  │     │  7    │ Labor - Assembly       │ 8h  │ null                 │     │
  │     └─────────────────────────────────────────────────────────────┘     │
  │                                                                         │
  └─────────────────────────────────────────────────────────────────────────┘

  ---
  Quotation Creation Workflow

  ┌─────────────────────────────────────────────────────────────────────────┐
  │  STEP 1: Select BoM Template                                            │
  ├─────────────────────────────────────────────────────────────────────────┤
  │                                                                         │
  │  Customer: PT Pabrik Tekstil Jaya                                       │
  │  Project: Panel Distribusi Gedung B                                     │
  │                                                                         │
  │  Select BoM: [Panel Distribusi 400A          ▼]                         │
  │  Quantity:   [1]                                                        │
  │                                                                         │
  │  [Generate Quotation Options →]                                         │
  │                                                                         │
  └─────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
  ┌─────────────────────────────────────────────────────────────────────────┐
  │  STEP 2: Configure Brand Combinations                                   │
  ├─────────────────────────────────────────────────────────────────────────┤
  │                                                                         │
  │  ┌─ Quick Presets ─────────────────────────────────────────────────┐    │
  │  │ [All ABB] [All Siemens] [All Schneider] [Budget Mix] [Custom]   │    │
  │  └─────────────────────────────────────────────────────────────────┘    │
  │                                                                         │
  │  Or configure per component:                                            │
  │                                                                         │
  │  ┌─────────────────────────────────────────────────────────────────┐    │
  │  │ Component       │ Option A    │ Option B    │ Option C          │    │
  │  ├─────────────────────────────────────────────────────────────────┤    │
  │  │ MCCB 400A 3P    │ ● ABB       │ ● Siemens   │ ● Schneider       │    │
  │  │                 │   Rp 4.500k │   Rp 4.200k │   Rp 4.800k       │    │
  │  ├─────────────────────────────────────────────────────────────────┤    │
  │  │ MCCB 100A 3P×4  │ ● ABB       │ ● Siemens   │ ● Schneider       │    │
  │  │                 │   Rp 8.400k │   Rp 7.600k │   Rp 8.800k       │    │
  │  ├─────────────────────────────────────────────────────────────────┤    │
  │  │ MCB 16A 1P ×12  │ ● ABB       │ ● Siemens   │ ● Schneider       │    │
  │  │                 │   Rp 1.800k │   Rp 1.560k │   Rp 1.920k       │    │
  │  ├─────────────────────────────────────────────────────────────────┤    │
  │  │ Busbar, Encl... │ (no alternatives - same across all options)   │    │
  │  ├─────────────────────────────────────────────────────────────────┤    │
  │  │ SUBTOTAL        │ Rp 45.2 jt  │ Rp 42.8 jt  │ Rp 47.1 jt        │    │
  │  │ + Margin 20%    │ Rp 54.2 jt  │ Rp 51.4 jt  │ Rp 56.5 jt        │    │
  │  └─────────────────────────────────────────────────────────────────┘    │
  │                                                                         │
  │  ☑ Option A (ABB Premium)                                               │
  │  ☑ Option B (Siemens Value)     ← Customer often picks middle option   │
  │  ☑ Option C (Schneider Premium)                                         │
  │  ☐ Option D (Budget Mix) - Auto-select cheapest per component           │
  │                                                                         │
  │  [Generate Selected Quotations →]                                       │
  │                                                                         │
  └─────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
  ┌─────────────────────────────────────────────────────────────────────────┐
  │  STEP 3: Review & Send                                                  │
  ├─────────────────────────────────────────────────────────────────────────┤
  │                                                                         │
  │  Created 3 Quotation Variants:                                          │
  │                                                                         │
  │  ┌────────────────────────────────────────────────────────────────┐     │
  │  │ QUO-202512-0042-A  │ ABB Premium      │ Rp 54.240.000 │ [Edit] │     │
  │  │ QUO-202512-0042-B  │ Siemens Value    │ Rp 51.360.000 │ [Edit] │     │
  │  │ QUO-202512-0042-C  │ Schneider Premium│ Rp 56.520.000 │ [Edit] │     │
  │  └────────────────────────────────────────────────────────────────┘     │
  │                                                                         │
  │  Comparison Summary for Customer:                                       │
  │  ┌────────────────────────────────────────────────────────────────┐     │
  │  │ Option   │ Brand    │ Total        │ Lead Time │ Warranty      │     │
  │  │ A        │ ABB      │ Rp 54.24 jt  │ 10 days   │ 2 years       │     │
  │  │ B ⭐     │ Siemens  │ Rp 51.36 jt  │ 14 days   │ 2 years       │     │
  │  │ C        │ Schneider│ Rp 56.52 jt  │ 12 days   │ 3 years       │     │
  │  └────────────────────────────────────────────────────────────────┘     │
  │                                                                         │
  │  [Preview PDF] [Send All to Customer] [Send Selected Only]              │
  │                                                                         │
  └─────────────────────────────────────────────────────────────────────────┘

  ---
  When Customer Approves

  Customer approves Option B (Siemens)
                │
                ▼
  ┌─────────────────────────────────────────────────────────────────────────┐
  │  QUO-202512-0042-B Approved                                             │
  ├─────────────────────────────────────────────────────────────────────────┤
  │                                                                         │
  │  Next Actions:                                                          │
  │                                                                         │
  │  [Create Project] → Auto-creates project with budget from quotation     │
  │                                                                         │
  │  [Create Work Order] → Uses the specific BoM variant (Siemens)          │
  │                        Materials already determined                      │
  │                                                                         │
  │  [Convert to Invoice] → DP invoice or full invoice                      │
  │                                                                         │
  │  [Generate PO] → Creates PO for Siemens components                      │
  │                  (checks stock, only PO what's needed)                  │
  │                                                                         │
  └─────────────────────────────────────────────────────────────────────────┘

 