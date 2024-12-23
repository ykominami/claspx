module Claspx
  class Cmd
    def initialize(name)
      @name = name
    end

    def execute(cmdline, dir = nil)
      if dir.nil?
        o, e, s = Open3.capture3(cmdline)
      else
        o, e, s = Open3.capture3(cmdline, chdir: dir)
      end

      if s.success?
        stdout = o.chomp
        result = Result.new(true, stdout)
        result.add_std_out_and_err(stdout, e)
        result.add_return_value(stdout)
      else
        stderr = e.chomp
        result = Result.new(false, stderr)
        result.add_std_out_and_err(o, e)
        result.add_return_value(stderr)
      end
    end

    def spawn(cmd_line, &block)
      if block_given?
        PTY.spawn(cmd_line, &block)
      else
        Result.new(true, "xxxx no_block cmd_line=#{cmd_line}")
      end
    end
  end
end
