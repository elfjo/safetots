library(stringr)

split_md_to_qmd_and_yaml <- function(file_path) {
  # Read the entire Markdown file as a vector of lines
  lines <- readLines(file_path, warn = FALSE)
  
  # Ensure there is a newline at the end of the file
  if (length(lines) > 0 && !grepl("\n$", tail(lines, 1))) {
    lines <- c(lines, "")
  }
  
  # Detect all second-level headings
  heading_indices <- grep("^## ", lines)
  if (length(heading_indices) == 0) {
    stop("No second-level headings found in the document.")
  }
  
  # Add the end of the file as the final part of the split
  heading_indices <- c(heading_indices, length(lines) + 1)
  
  # Filenames and chapters to be used in the YAML
  chapter_files <- vector("list", length(heading_indices) - 1)
  
  for (i in seq_along(heading_indices)[-length(heading_indices)]) {
    start_line <- heading_indices[i]
    end_line <- heading_indices[i + 1] - 1
    
    # Extract the title for filename
    title <- sub("^## ", "", lines[start_line])
    filename <- paste0(str_trim(gsub("[^A-Za-z0-9 ]", "", title)), ".qmd")
    
    # Write each section to a .qmd file
    writeLines(lines[start_line:end_line], filename)
    chapter_files[[i]] <- filename
  }
  
  # Generate YAML configuration
  yaml_content <- c(
    "project:",
    "  type: book",
    "",
    "book:",
    "  title: \"Safetots\"",
    "  author: \"Norah Jones\"",
    "  date: \"4/27/2024\"",
    "  chapters:",
    paste("    -", unlist(chapter_files)),
    "",
    "bibliography: references.bib",
    "",
    "format:",
    "  html:",
    "    theme: cosmo",
    "  pdf:",
    "    documentclass: scrreprt",
    "",
    "editor: visual"
  )
  
  # Write YAML content to file
  writeLines(yaml_content, "book_config.yaml")
}

# Replace 'path/to/your/Safetots.md' with the actual path to your file
split_md_to_qmd_and_yaml("path/to/your/Safetots.md")
