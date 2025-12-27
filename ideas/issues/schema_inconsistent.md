# Schema Issue

  ---
  1. Schema Inconsistencies Found

  Critical Issues (High Severity)

  | #   | Flow       | Issue                                                                              | Impact                                                           |
  |-----|------------|------------------------------------------------------------------------------------|------------------------------------------------------------------|
  | 1   | Sales      | quotation_items missing revenue_account_id                                         | Revenue account must be inferred from product; no direct mapping |
  | 2   | Purchase   | purchase_order_items has quantity_received, last_received_at - bill_items does NOT | Receiving data lost in PO→Bill conversion                        |
  | 3   | Purchase   | purchase_order_items missing expense_account_id                                    | Inconsistent account mapping with bills                          |
  | 4   | Returns    | sales_return_items uses flat amount vs invoice's line_total with tax/discount      | Cannot properly reverse invoice with correct tax breakdown       |
  | 5   | Returns    | purchase_return_items same issue with bills                                        | Cannot properly reverse bill with correct tax breakdown          |
  | 6   | Production | work_order_items lacks account_id                                                  | Cannot auto-generate WIP journal entries                         |
  | 7   | Production | material_requisition_items missing unit_cost                                       | Cannot pre-calculate budget impact at requisition stage          |

  Medium Severity Issues

  | #   | Flow       | Issue                                                 | Impact                                         |
  |-----|------------|-------------------------------------------------------|------------------------------------------------|
  | 8   | Inventory  | delivery_order_items missing unit_price, cost fields  | Cannot calculate COGS at delivery              |
  | 9   | Inventory  | goods_receipt_note_items missing tax/discount fields  | Cannot match GRN to bill financially           |
  | 10  | Cross-flow | Payments use polymorphic payable_type/id vs direct FK | Harder to trace payment-document relationships |

  ---
  2. Accounting Reports Status

  Financial Statements

  | Report                         | Status      | Notes                     |
  |--------------------------------|-------------|---------------------------|
  | Balance Sheet (Neraca)         | ✅ Complete | With comparative analysis |
  | Income Statement (Laba Rugi)   | ✅ Complete | With COGS detail          |
  | Cash Flow Statement            | ✅ Complete | Three-activity method     |
  | Statement of Changes in Equity | ❌ Missing  | Not implemented           |

  Supporting Reports

  | Report                  | Status      | Notes                                            |
  |-------------------------|-------------|--------------------------------------------------|
  | Trial Balance           | ✅ Complete |                                                  |
  | General Ledger          | ✅ Complete | Full detail view                                 |
  | AR Aging (Umur Piutang) | ✅ Complete | Configurable buckets                             |
  | AP Aging (Umur Hutang)  | ✅ Complete |                                                  |
  | Bank Reconciliation     | ⚠️ Partial  | Transaction matching only, missing formal report |

  Tax/Compliance Reports

  | Report           | Status      | Notes                |
  |------------------|-------------|----------------------|
  | VAT/PPN Summary  | ✅ Complete | Indonesia PPN format |
  | Tax Invoice List | ✅ Complete |                      |
  | Input Tax List   | ✅ Complete |                      |

  Management Reports

  | Report                | Status      | Notes                               |
  |-----------------------|-------------|-------------------------------------|
  | Budget vs Actual      | ✅ Complete | Monthly breakdown                   |
  | Project Profitability | ✅ Complete | Comprehensive                       |
  | Inventory Valuation   | ✅ Complete | Weighted average                    |
  | COGS Analysis         | ⚠️ Partial  | Basic tracking, no dedicated report |
  | Work Order Costs      | ✅ Complete | With variance analysis              |
  | Subcontractor Reports | ✅ Complete | Retention tracking                  |

  ---
  Priority Recommendations

  Schema Fixes (in priority order)

  1. Add quantity_invoiced to purchase_order_items - Track how much of a PO item has been billed
  2. Add account fields to quotation_items - revenue_account_id for consistent conversion
  3. Enhance return items - Add discount_percent, discount_amount, tax_rate, tax_amount, line_total to match source documents
  4. Add expense_account_id to purchase_order_items - Consistent with bill_items
  5. Add pricing to delivery_order_items - unit_price, discount, tax for COGS tracking
  6. Add unit_cost to material_requisition_items - Enable budget variance at requisition stage

  Missing Reports (in priority order)

  1. Statement of Changes in Equity - Required for complete financial statements
  2. Bank Reconciliation Report - Formal report showing unreconciled items
  3. COGS Detail Report - Analysis by product/category