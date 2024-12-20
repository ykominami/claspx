require 'rkelly'

module Claspx
  class Jsscript
    class JsscriptPair
      attr_reader :src_file, :dest_file
      def initialize(src_file, dest_file)
        @src_file = src_file
        @dest_file = dest_file
      end

      def copy
        FileUtils.cp(@src_file, @dest_file)
      end
    end

    class Filex
      attr_accessor :path, :content
      def initialize(path, content=nil)
        @path = path
        @content = content
      end

      def exist?
        @path.exist?
      end

      def load
        @content = File.read(@path.to_s)
        self
      end
    end

    class JsscriptPart
      attr_reader :part0, :part1, :part2
      def initialize(parts)
        @part0 = parts[0]
        @part1 = parts[1]
        @part2 = parts[2]
      end
    end

    attr_reader :src_file, :work_head_file, :work_content_file, :work_foot_file,
                :work_import_file, :work_export_file
    def initialize(src_file)
      @buffer_initial = [[], [], []]

      @src_file = src_file
      @work_head_file = nil
      @work_content_file = nil
      @work_foot_file = nil
      @work_import_file = nil
      @work_export_file = nil
    end
 
    def load_filex(src_file)
      Filex.new(src_file).load
    end

    def make_file_array(content_parts)
      [content_parts.part0, content_parts.part1, content_parts.part2]
    end

    def make_filex_base(dir_pn, basename, ext_array)
      ext_array.map{ |ext|
        Filex.new(dir_pn.join("#{basename}#{ext}"))
      }
    end
      
    def make_filex_for_3parts(dir_pn, basename)
      make_filex_base(dir_pn, basename, %W(.head .content .foot))
    end

    def make_filex_for_esmodule(dir_pn, basename)
      make_filex_base(dir_pn, basename, %W(.import .export))
    end

    def analyze(javascript_code)
      parser = RKelly::Parser.new
      ast = parser.parse(javascript_code)
      pp ast
      @work_import_file = nil
      @work_export_file = nil
      JsscriptPart.new([@work_import_file, nil, @work_export_file])
    end

    def make_content_part(dir_pn, basename)
      content_filex_array = make_filex_for_3parts(dir_pn, basename)
      # pp "content_filex_array==="
      # pp content_filex_array
      JsscriptPart.new(content_filex_array)
    end

    def convertx(work_file, dir_pn, basename)
      content_part = make_content_part(dir_pn, basename)
      convert_with_parts(work_file, content_part)
    end

    def make_file_array(conten_parts)
      [conten_parts.part0.path, conten_parts.part1.path, conten_parts.part2.path]
    end

    def convert_with_parts(work_file, content_parts)
      @work_head_file, @work_content_file, @work_foot_file = make_file_array(content_parts)

      ret = reform(work_file)
      return ret if ret.error?

      buffers = ret.return_value

      content_parts.part0.content = buffers[0].join("\n")
      File.write(@work_head_file, content_parts.part0.content)
      
      content_parts.part1.content  = buffers[1].join("\n")
      File.write(@work_content_file, content_parts.part1.content)

      content_parts.part2.content  = buffers[2].join("\n")
      File.write(@work_foot_file, content_parts.part2.content)


      # JsscriptPair(dest_content_file, @work_file)
      status = true
      result = Result.new(status, "Clapsx#reform_js_script")
      result.add_return_value( content_parts )
      result.add_return_array( content_parts )
    end
  
    def convert(work_file, work_head_file, work_content_file, work_foot_file)
      ret = reform(work_file)
      return ret if ret.error?

      @work_head_file = work_head_file
      @work_content_file = work_content_file
      @work_foot_file = work_foot_file
      buffers = ret.return_value

      content_head = buffers[0].join("\n")
      File.write(dest_head_file, content_head)
      
      content = buffers[1].join("\n")
      File.write(dest_content_file, content)

      content_foot = buffers[2].join("\n")
      File.write(dest_foot_file, content_foot)

      import_part, export_part = analyze(content)


      # JsscriptPair(dest_content_file, @work_file)
      status = true
      Result.new(status, "Clapsx#reform_js_script")
    end

    def reform(src_file)
      buffer_size = @buffer_initial.size

      # lines = File.readlines(src_file)
      lines = File.read(src_file)
      buffer_index = 0
      buffers = lines.each_with_object(@buffer_initial){ |line, buffer|
        if line =~ /^```javascript/
          buffer[buffer_index] << line
          buffer_index += 1 if buffer_index < buffer_size - 1
        elsif line =~ /^```/
          buffer_index += 1 if buffer_index < buffer_size - 1
          buffer[buffer_index] << line
        else
          buffer[buffer_index] << line
        end
      }
      status = true
      result = Result.new(status, "Clapsx#reform_js_script")
      result.add_return_value(buffers)
      result.add_return_array(buffers)
    end

    def transformed_js_and_src_file
      JsscriptPair.new(@src_file, @dest_content_file )
      # [@src_file , @dest_content_file]
    end

    def copy_transformed_js_to_dest
      FileUtils.cp(@dest_content_file, @work_file)
    end
  end
end    
