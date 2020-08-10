---
title: "This is a user friendly name of the template"
resolution_context:
  required_pdis:
    - pdi:case/*/some_dataset_with_variable_b
    - pdi:patient/*/other_dataset_with_variable_a
additional_output_fields:
  subject_line: "subject {{patient.variable_a}}"
  foo: "Bar"
---

"Body copy {{case.variable_b}}"

## efe
- f
- f