# Apporval Workflows
## Definition
  Approval Workflows - What It Means

  An approval workflow is a multi-step authorization process before a document/transaction becomes "official" or actionable.


## Current State
  ---
  Current State (No Workflow)

  Right now, your system has simple status transitions:

  Quotation:  Draft → Submitted → Approved ✓
  PO:         Draft → Submitted → Approved ✓

  But there's no control over WHO can approve, WHAT amounts require approval, or HOW MANY levels of approval are needed.

  ---
  What Approval Workflows Would Look Like

  Example 1: Purchase Order Approval (Most Common)

  ┌─────────────────────────────────────────────────────────────────┐
  │  PO Amount         │  Required Approvals                       │
  ├─────────────────────────────────────────────────────────────────┤
  │  < Rp 5 juta       │  Auto-approved (Purchasing staff)         │
  │  Rp 5-50 juta      │  Purchasing Manager                       │
  │  Rp 50-200 juta    │  Purchasing Manager → Finance Manager     │
  │  > Rp 200 juta     │  Purchasing Mgr → Finance Mgr → Director  │
  └─────────────────────────────────────────────────────────────────┘

  Why important for your customers:
  - Electrical panel projects often have large material purchases (ABB/Siemens components are expensive)
  - Solar EPC buys panels in bulk - needs financial controls

  Example 2: Quotation Approval (Discount Control)

  ┌─────────────────────────────────────────────────────────────────┐
  │  Condition                    │  Required Approval              │
  ├─────────────────────────────────────────────────────────────────┤
  │  Standard margin (≥20%)       │  Sales person can send directly │
  │  Reduced margin (10-20%)      │  Sales Manager approval         │
  │  Low margin (<10%)            │  Director approval              │
  │  Special payment terms        │  Finance Manager approval       │
  └─────────────────────────────────────────────────────────────────┘

  Why important:
  - Prevents sales giving away too much discount without oversight
  - Protects profit margins on competitive bids

  Example 3: Subcontractor Work Order Approval

  ┌─────────────────────────────────────────────────────────────────┐
  │  Subcontractor WO             │  Required Approval              │
  ├─────────────────────────────────────────────────────────────────┤
  │  Existing subcontractor       │  Project Manager                │
  │  New subcontractor            │  Project Mgr → Operations Dir   │
  │  Value > Rp 100 juta          │  + Finance Manager              │
  └─────────────────────────────────────────────────────────────────┘

  Example 4: Material Requisition Approval

  ┌─────────────────────────────────────────────────────────────────┐
  │  MR Type                      │  Required Approval              │
  ├─────────────────────────────────────────────────────────────────┤
  │  Within project budget        │  Auto-approved                  │
  │  Exceeds project budget       │  Project Manager                │
  │  Emergency/rush request       │  Operations Manager             │
  └─────────────────────────────────────────────────────────────────┘

  ---
  Technical Implementation Concept

  ┌──────────────────┐     ┌──────────────────┐     ┌──────────────────┐
  │ approval_rules   │     │ approval_requests│     │ approval_actions │
  ├──────────────────┤     ├──────────────────┤     ├──────────────────┤
  │ document_type    │     │ approvable_type  │     │ request_id       │
  │ condition_field  │     │ approvable_id    │     │ approver_id      │
  │ condition_op     │     │ current_step     │     │ action (approve/ │
  │ condition_value  │     │ status           │     │   reject/delegate│
  │ approval_steps[] │     │ requested_at     │     │ comments         │
  │ is_active        │     │ completed_at     │     │ acted_at         │
  └──────────────────┘     └──────────────────┘     └──────────────────┘

  ---
  ## User Experience Flow

  Sales creates Quotation with 8% margin
           │
           ▼
  ┌─────────────────────────────────────┐
  │  ⚠️ Approval Required               │
  │  Margin below 10% threshold         │
  │                                     │
  │  Approver: Budi (Sales Manager)     │
  │  [Submit for Approval]              │
  └─────────────────────────────────────┘
           │
           ▼
  Budi receives notification
           │
           ▼
  ┌─────────────────────────────────────┐
  │  📋 Pending Approval                │
  │  QUO-202512-0042                    │
  │  Customer: PT Solar Jaya            │
  │  Amount: Rp 850.000.000             │
  │  Margin: 8% (below standard 20%)    │
  │                                     │
  │  [Approve] [Reject] [Request Info]  │
  └─────────────────────────────────────┘

  ---
  Priority Assessment

  | Document Type        | Priority  | Reason                                  |
  |----------------------|-----------|-----------------------------------------|
  | Purchase Order       | 🔴 High   | Large material costs, financial control |
  | Quotation (discount) | 🟡 Medium | Margin protection                       |
  | Subcontractor WO     | 🟡 Medium | External cost control                   |
  | Material Requisition | 🟢 Low    | Internal, lower risk                    |
  | Payment/Bill         | 🟢 Low    | Usually post-fact                       |

  ---
  
  1. Requires organizational structure - You need clear role hierarchy defined
  2. Complex to implement well - Notifications, delegation, escalation, timeout rules
  3. Not blocking daily operations - Business can run without it (just less controlled)

  However, if your target customers are larger SMEs with multiple departments, this could move to Phase 2.

  ---
  